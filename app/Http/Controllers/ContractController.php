<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\ContractMember;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Property;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
        }

        $query = Contract::with(['room.property', 'tenant', 'members']);

        if ($status) {
            $query->where('status', $status);
        }
        if ($propertyId) {
            $query->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('contracts.index', compact('contracts', 'properties', 'status', 'propertyId'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $roomsQuery = Room::where('status', 'available')->with('property');
        if ($user && !$user->isAdmin()) {
            $roomsQuery->where('property_id', $user->property_id);
            $tenants = Tenant::where(function ($q) use ($user) {
                $q->whereHas('contracts.room', fn($r) => $r->where('property_id', $user->property_id))
                  ->orWhereDoesntHave('contracts');
            })->get();
        } else {
            $tenants = Tenant::all();
        }
        $rooms = $roomsQuery->get();
        $selectedRoomId = $request->query('room_id');

        return view('contracts.create', compact('rooms', 'tenants', 'selectedRoomId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rental_price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'deposit_status' => 'required|in:held,refunded,deducted',
            'terms' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            if ($room->property_id != $user->property_id) {
                abort(403, 'Bạn không có quyền lập hợp đồng cho phòng thuộc cơ sở khác.');
            }

            $tenant = Tenant::findOrFail($validated['tenant_id']);
            $hasAnyContract = $tenant->contracts()->exists();
            $hasContractInProperty = $tenant->contracts()->whereHas('room', fn($r) => $r->where('property_id', $user->property_id))->exists();
            if ($hasAnyContract && !$hasContractInProperty) {
                abort(403, 'Bạn không thể lập hợp đồng cho khách thuê thuộc cơ sở khác.');
            }
        }

        // Tự động sinh mã hợp đồng HD-YYYYMM-ROOM
        $code = 'HD-' . now()->format('Ym') . '-' . $room->room_number . '-' . rand(100, 999);
        $validated['contract_code'] = $code;
        $validated['status'] = 'active';

        $contract = Contract::create($validated);

        // Đổi trạng thái phòng sang 'occupied' (Đang cho thuê)
        $room->update(['status' => 'occupied']);

        return redirect()->route('contracts.show', $contract->id)->with('success', 'Ký hợp đồng thuê phòng thành công!');
    }

    public function show(Contract $contract)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xem hợp đồng thuộc cơ sở khác.');
        }

        $contract->load(['room.property', 'room.assets', 'room.fees', 'tenant', 'members', 'invoices' => fn($q) => $q->orderBy('year', 'desc')->orderBy('month', 'desc')]);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa hợp đồng thuộc cơ sở khác.');
        }

        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền cập nhật hợp đồng thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rental_price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'deposit_status' => 'required|in:held,refunded,deducted',
            'status' => 'required|in:active,expiring_soon,terminated',
            'terms' => 'nullable|string',
        ]);

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract->id)->with('success', 'Cập nhật hợp đồng thành công!');
    }

    // Thêm người ở cùng vào hợp đồng
    public function addMember(Request $request, Contract $contract)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền thêm thành viên cho hợp đồng thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'id_card_number' => 'nullable|string|max:25',
            'relationship' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50',
        ]);

        $contract->members()->create($validated);
        return back()->with('success', 'Đã thêm thành viên ở cùng phòng!');
    }

    // Xóa người ở cùng
    public function deleteMember(ContractMember $member)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $member->contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa thành viên của hợp đồng thuộc cơ sở khác.');
        }

        $member->delete();
        return back()->with('success', 'Đã xóa thành viên khỏi hợp đồng!');
    }

    // In Hợp đồng thuê phòng & Biên bản bàn giao tài sản chuẩn pháp lý
    public function print(Contract $contract)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền in hợp đồng thuộc cơ sở khác.');
        }

        $contract->load(['room.property', 'room.assets', 'room.fees', 'tenant', 'members']);
        return view('contracts.print', compact('contract'));
    }

    // Form Trả phòng / Quyết toán cọc (Check-out)
    public function showCheckout(Contract $contract)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền trả phòng cho hợp đồng thuộc cơ sở khác.');
        }

        $contract->load(['room.property', 'room.assets', 'tenant', 'invoices']);
        $latestElec = $contract->room->getLatestElectricityReading();
        $latestWater = $contract->room->getLatestWaterReading();

        return view('contracts.checkout', compact('contract', 'latestElec', 'latestWater'));
    }

    // Xử lý Trả phòng / Quyết toán cọc
    public function processCheckout(Request $request, Contract $contract)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền thanh lý hợp đồng thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'final_electricity' => 'required|numeric|min:0',
            'final_water' => 'required|numeric|min:0',
            'damage_deduction' => 'nullable|numeric|min:0',
            'unpaid_bills_deduction' => 'nullable|numeric|min:0',
            'refund_amount' => 'required|numeric',
            'checkout_note' => 'nullable|string',
            'room_next_status' => 'required|in:available,maintenance',
        ]);

        // Cập nhật hợp đồng đã thanh lý
        $contract->update([
            'status' => 'terminated',
            'deposit_status' => $validated['refund_amount'] > 0 ? 'refunded' : 'deducted',
            'terminated_at' => now()->toDateString(),
            'checkout_note' => $validated['checkout_note'] . " (Khấu trừ hỏng hóc: " . number_format($validated['damage_deduction'] ?? 0) . "đ, Khấu trừ nợ: " . number_format($validated['unpaid_bills_deduction'] ?? 0) . "đ, Hoàn cọc thực trả: " . number_format($validated['refund_amount']) . "đ)",
        ]);

        // Đổi trạng thái phòng
        $contract->room->update([
            'status' => $validated['room_next_status'],
            'initial_electricity' => $validated['final_electricity'],
            'initial_water' => $validated['final_water'],
        ]);

        return redirect()->route('contracts.show', $contract->id)->with('success', 'Thanh lý hợp đồng và trả phòng thành công!');
    }

    public function destroy(Contract $contract)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $contract->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa hợp đồng thuộc cơ sở khác.');
        }

        if ($contract->status === 'active') {
            return back()->with('error', 'Không thể xóa hợp đồng đang có hiệu lực! Hãy làm thủ tục trả phòng/thanh lý trước.');
        }

        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Đã xóa hợp đồng!');
    }
}
