<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Property;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
        }
        $status = $request->query('status');

        $query = Invoice::with(['room.property', 'room.fees', 'contract.tenant', 'contract.members'])
            ->where('month', $month)
            ->where('year', $year);

        if ($propertyId) {
            $query->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
        }

        if ($status) {
            if ($status === 'overdue') {
                $query->where('status', '!=', 'paid')->whereDate('due_date', '<', now()->startOfDay());
            } else {
                $query->where('status', $status);
            }
        }

        $invoices = $query->orderBy('due_date', 'asc')->get();

        // Tổng kết nhanh của tháng
        $totalAmount = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalRemaining = $invoices->sum('remaining_amount');

        return view('invoices.index', compact(
            'invoices',
            'properties',
            'month',
            'year',
            'propertyId',
            'status',
            'totalAmount',
            'totalPaid',
            'totalRemaining'
        ));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $properties = Property::where('id', $user->property_id)->get();
            $rooms = Room::where('status', 'occupied')->where('property_id', $user->property_id)->with('property')->get();
        } else {
            $properties = Property::all();
            $rooms = Room::where('status', 'occupied')->with('property')->get();
        }

        $roomId = $request->query('room_id');
        $room = $roomId ? Room::with(['fees', 'currentContract.tenant'])->find($roomId) : null;
        if ($room && $user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền lập hóa đơn cho phòng thuộc cơ sở khác.');
        }

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $defaultOldElec = $room ? $room->getLatestElectricityReading() : 0;
        $defaultOldWater = $room ? $room->getLatestWaterReading() : 0;

        return view('invoices.create', compact('properties', 'rooms', 'room', 'month', 'year', 'defaultOldElec', 'defaultOldWater'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2050',
            'due_date' => 'required|date',
            'electricity_old' => 'required|numeric|min:0',
            'electricity_new' => 'required|numeric|gte:electricity_old',
            'electricity_rate' => 'required|numeric|min:0',
            'water_old' => 'required|numeric|min:0',
            'water_new' => 'required|numeric|gte:water_old',
            'water_rate' => 'required|numeric|min:0',
            'room_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $room = Room::with(['currentContract', 'fees'])->findOrFail($validated['room_id']);

        $user = $request->user();
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền lập hóa đơn cho phòng thuộc cơ sở khác.');
        }
        $tenantsCount = $room->currentContract ? ($room->currentContract->members()->count() + 1) : 1;

        // 1. Tính điện
        $elecUsage = $validated['electricity_new'] - $validated['electricity_old'];
        $elecTotal = $elecUsage * $validated['electricity_rate'];

        // 2. Tính nước theo đúng cấu hình của phòng (meter, per_person, fixed_room)
        $waterCalcType = $room->water_calculation_type ?: 'meter';
        if ($waterCalcType === 'per_person') {
            $waterUsage = $tenantsCount;
            $waterRate = (float) $validated['water_rate'];
            $waterTotal = $waterUsage * $waterRate;
            $waterOld = 0;
            $waterNew = $tenantsCount;
        } elseif ($waterCalcType === 'fixed_room') {
            $waterUsage = 1;
            $waterRate = (float) $validated['water_rate'];
            $waterTotal = $waterRate;
            $waterOld = 0;
            $waterNew = 1;
        } else {
            $waterOld = (float) $validated['water_old'];
            $waterNew = (float) $validated['water_new'];
            $waterUsage = max(0, $waterNew - $waterOld);
            $waterRate = (float) $validated['water_rate'];
            $waterTotal = $waterUsage * $waterRate;
        }

        // 3. Tính phí dịch vụ (bao gồm tiền mạng Internet Wifi theo đầu người hoặc theo phòng)
        $feesDetail = [];
        $otherFeesTotal = 0;

        foreach ($room->fees as $fee) {
            $feeAmount = $fee->calculateTotal($tenantsCount);
            $desc = $fee->fee_type === 'per_person' 
                ? "{$tenantsCount} người × " . number_format($fee->unit_price, 0, ',', '.') . "đ"
                : "Khoán cố định phòng";

            $feesDetail[] = [
                'name' => $fee->fee_name,
                'amount' => (int) $feeAmount,
                'type' => $fee->fee_type,
                'calc_desc' => $desc,
            ];
            $otherFeesTotal += $feeAmount;
        }

        $discount = $validated['discount'] ?? 0;
        $totalAmount = $validated['room_price'] + $elecTotal + $waterTotal + $otherFeesTotal - $discount;

        $invoiceCode = "INV-{$validated['year']}" . str_pad($validated['month'], 2, '0', STR_PAD_LEFT) . "-P{$room->room_number}";

        $invoice = Invoice::updateOrCreate(
            ['invoice_code' => $invoiceCode],
            [
                'contract_id' => $room->currentContract?->id,
                'room_id' => $room->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'from_date' => Carbon::create($validated['year'], $validated['month'], 1)->startOfMonth()->toDateString(),
                'to_date' => Carbon::create($validated['year'], $validated['month'], 1)->endOfMonth()->toDateString(),
                'due_date' => $validated['due_date'],
                'electricity_old' => $validated['electricity_old'],
                'electricity_new' => $validated['electricity_new'],
                'electricity_usage' => $elecUsage,
                'electricity_rate' => $validated['electricity_rate'],
                'electricity_total' => $elecTotal,
                'water_old' => $waterOld,
                'water_new' => $waterNew,
                'water_usage' => $waterUsage,
                'water_rate' => $waterRate,
                'water_total' => $waterTotal,
                'water_calculation_type' => $waterCalcType,
                'room_price' => $validated['room_price'],
                'fees_detail' => $feesDetail,
                'other_fees' => $otherFeesTotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'remaining_amount' => $totalAmount,
                'status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Lập hóa đơn thành công!');
    }

    // CHỐT ĐIỆN NƯỚC HÀNG LOẠT CHO CẢ NHÀ TRỌ (BULK ENTRY)
    public function bulkCreate(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
            if (!$propertyId && $properties->isNotEmpty()) {
                $propertyId = $properties->first()->id;
            }
        }
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $selectedProperty = $propertyId ? Property::find($propertyId) : null;

        // Lấy tất cả các phòng đang có khách thuê của nhà trọ này
        $rooms = $selectedProperty ? $selectedProperty->rooms()
            ->where('status', 'occupied')
            ->with(['currentContract.tenant', 'currentContract.members', 'fees'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get() : collect();

        // Gắn số điện nước cũ gần nhất cho từng phòng
        foreach ($rooms as $room) {
            $room->old_elec = $room->getLatestElectricityReading();
            $room->old_water = $room->getLatestWaterReading();
        }

        return view('invoices.bulk_create', compact('properties', 'selectedProperty', 'propertyId', 'month', 'year', 'rooms'));
    }

    // LƯU HÓA ĐƠN HÀNG LOẠT
    public function bulkStore(Request $request)
    {
        $user = $request->user();
        $propertyId = $request->input('property_id');

        if ($user && !$user->isAdmin() && $propertyId != $user->property_id) {
            abort(403, 'Bạn không có quyền chốt hóa đơn hàng loạt cho cơ sở khác.');
        }

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $dueDate = $request->input('due_date');
        $roomsData = $request->input('rooms', []);

        $createdCount = 0;

        foreach ($roomsData as $roomId => $data) {
            if (!isset($data['electricity_new']) || $data['electricity_new'] === '') {
                continue; // Bỏ qua nếu chưa nhập số mới
            }

            $room = Room::with(['currentContract', 'fees'])->find($roomId);
            if (!$room) continue;

            if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
                continue;
            }

            $elecOld = (float) ($data['electricity_old'] ?? 0);
            $elecNew = (float) $data['electricity_new'];
            $elecUsage = max(0, $elecNew - $elecOld);
            $elecRate = (float) ($data['electricity_rate'] ?? $room->electricity_rate);
            $elecTotal = $elecUsage * $elecRate;

            // 2. Tính nước theo đúng cấu hình của phòng (meter, per_person, fixed_room)
            $waterCalcType = $room->water_calculation_type ?: 'meter';
            $tenantsCount = $room->currentContract ? ($room->currentContract->members()->count() + 1) : 1;

            if ($waterCalcType === 'per_person') {
                $waterOld = 0;
                $waterNew = $tenantsCount;
                $waterUsage = $tenantsCount;
                $waterRate = (float) ($data['water_rate'] ?? $room->water_rate);
                $waterTotal = $waterUsage * $waterRate;
            } elseif ($waterCalcType === 'fixed_room') {
                $waterOld = 0;
                $waterNew = 1;
                $waterUsage = 1;
                $waterRate = (float) ($data['water_rate'] ?? $room->water_rate);
                $waterTotal = $waterRate;
            } else {
                $waterOld = (float) ($data['water_old'] ?? 0);
                $waterNew = (float) ($data['water_new'] ?? $waterOld);
                $waterUsage = max(0, $waterNew - $waterOld);
                $waterRate = (float) ($data['water_rate'] ?? $room->water_rate);
                $waterTotal = $waterUsage * $waterRate;
            }

            // 3. Tính phí dịch vụ (bao gồm tiền mạng Internet Wifi theo đầu người hoặc theo phòng)
            $feesDetail = [];
            $otherFeesTotal = 0;

            foreach ($room->fees as $fee) {
                $feeAmount = $fee->calculateTotal($tenantsCount);
                $desc = $fee->fee_type === 'per_person' 
                    ? "{$tenantsCount} người × " . number_format($fee->unit_price, 0, ',', '.') . "đ"
                    : "Khoán cố định phòng";

                $feesDetail[] = [
                    'name' => $fee->fee_name,
                    'amount' => (int) $feeAmount,
                    'type' => $fee->fee_type,
                    'calc_desc' => $desc,
                ];
                $otherFeesTotal += $feeAmount;
            }

            $roomPrice = (float) ($data['room_price'] ?? $room->price);
            $totalAmount = $roomPrice + $elecTotal + $waterTotal + $otherFeesTotal;

            $invoiceCode = "INV-{$year}" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-P{$room->room_number}";

            Invoice::updateOrCreate(
                ['invoice_code' => $invoiceCode],
                [
                    'contract_id' => $room->currentContract?->id,
                    'room_id' => $room->id,
                    'month' => $month,
                    'year' => $year,
                    'from_date' => Carbon::create($year, $month, 1)->startOfMonth()->toDateString(),
                    'to_date' => Carbon::create($year, $month, 1)->endOfMonth()->toDateString(),
                    'due_date' => $dueDate,
                    'electricity_old' => $elecOld,
                    'electricity_new' => $elecNew,
                    'electricity_usage' => $elecUsage,
                    'electricity_rate' => $elecRate,
                    'electricity_total' => $elecTotal,
                    'water_old' => $waterOld,
                    'water_new' => $waterNew,
                    'water_usage' => $waterUsage,
                    'water_rate' => $waterRate,
                    'water_total' => $waterTotal,
                    'water_calculation_type' => $waterCalcType,
                    'room_price' => $roomPrice,
                    'fees_detail' => $feesDetail,
                    'other_fees' => $otherFeesTotal,
                    'discount' => 0,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'remaining_amount' => $totalAmount,
                    'status' => 'unpaid',
                ]
            );

            $createdCount++;
        }

        return redirect()->route('invoices.index', ['property_id' => $propertyId, 'month' => $month, 'year' => $year])
            ->with('success', "Đã tạo/cập nhật thành công {$createdCount} hóa đơn cho tòa nhà!");
    }

    public function show(Invoice $invoice)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $invoice->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xem hóa đơn thuộc cơ sở khác.');
        }

        $invoice->load(['room.property', 'contract.tenant', 'contract.members', 'feedbacks.tenant']);
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $invoice->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền in hóa đơn thuộc cơ sở khác.');
        }

        $invoice->load(['room.property', 'contract.tenant']);
        return view('invoices.print', compact('invoice'));
    }

    // Ghi nhận thanh toán
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $invoice->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền cập nhật thanh toán cho hóa đơn thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:transfer,cash',
            'payment_date' => 'required|date',
            'payment_note' => 'nullable|string',
        ]);

        $newPaidAmount = $invoice->paid_amount + $validated['payment_amount'];
        $remaining = max(0, $invoice->total_amount - $newPaidAmount);

        $status = 'partially_paid';
        if ($remaining <= 0) {
            $status = 'paid';
        }

        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'remaining_amount' => $remaining,
            'status' => $status,
            'payment_method' => $validated['payment_method'],
            'paid_at' => $validated['payment_date'],
            'notes' => ($invoice->notes ? $invoice->notes . "\n" : "") . "Thanh toán " . number_format($validated['payment_amount']) . "đ ngày " . $validated['payment_date'] . " ({$validated['payment_method']}): " . ($validated['payment_note'] ?? ''),
        ]);

        return back()->with('success', 'Ghi nhận thanh toán hóa đơn thành công!');
    }

    public function destroy(Invoice $invoice)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $invoice->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa hóa đơn thuộc cơ sở khác.');
        }

        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Đã xóa hóa đơn!');
    }
}
