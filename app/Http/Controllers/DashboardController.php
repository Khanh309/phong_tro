<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Room;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\PropertyExpense;
use App\Models\InvoiceFeedback;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isManager = $user && !$user->isAdmin();

        $propertiesQuery = Property::withCount([
            'rooms',
            'rooms as occupied_rooms_count' => fn($q) => $q->where('status', 'occupied'),
            'rooms as available_rooms_count' => fn($q) => $q->where('status', 'available'),
            'rooms as maintenance_rooms_count' => fn($q) => $q->where('status', 'maintenance'),
        ])->with(['rooms' => fn($q) => $q->with('currentContract.members')]);

        if ($isManager) {
            $propertyId = $user->property_id;
            $properties = $propertiesQuery->where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = $propertiesQuery->get();
        }

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        // Query cơ bản theo bộ lọc nhà trọ
        $roomsQuery = Room::query();
        $invoicesQuery = Invoice::where('month', $month)->where('year', $year);
        $expensesQuery = PropertyExpense::where('month', $month)->where('year', $year);
        $contractsQuery = Contract::where('status', '!=', 'terminated');

        if ($propertyId) {
            $roomsQuery->where('property_id', $propertyId);
            $invoicesQuery->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
            $expensesQuery->where('property_id', $propertyId);
            $contractsQuery->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
        }

        // 1. Thống kê Phòng
        $totalRooms = $roomsQuery->count();
        $occupiedRooms = (clone $roomsQuery)->where('status', 'occupied')->count();
        $availableRooms = (clone $roomsQuery)->where('status', 'available')->count();
        $maintenanceRooms = (clone $roomsQuery)->where('status', 'maintenance')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // 2. Dòng THU (Từ khách thuê)
        $invoices = $invoicesQuery->with(['room.property', 'contract.tenant'])->get();
        $totalInvoiceAmount = $invoices->sum('total_amount'); // Tổng tiền cần thu
        $totalCollected = $invoices->sum('paid_amount');       // Thực thu
        $totalUnpaid = $invoices->sum('remaining_amount');    // Còn nợ chưa thu

        // 3. Dòng CHI (Trả cho Nhà nước & Đơn vị vận hành)
        $expenses = $expensesQuery->with('property')->get();
        $totalExpenses = $expenses->sum('amount'); // Tổng chi ra
        
        // Chi tiết từng loại chi phí đầu ra
        $expenseElecEvn = $expenses->where('expense_type', 'electricity_evn')->sum('amount');
        $expenseWaterSupply = $expenses->where('expense_type', 'water_supply')->sum('amount');
        $expenseStateTax = $expenses->where('expense_type', 'state_tax')->sum('amount');
        $expenseOther = $totalExpenses - ($expenseElecEvn + $expenseWaterSupply + $expenseStateTax);

        // 4. LỢI NHUẬN RÒNG (NET PROFIT = THỰC THU - THỰC CHI)
        $netProfit = $totalCollected - $totalExpenses;

        // 5. Cảnh báo quá hạn nộp tiền (Overdue invoices)
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now()->startOfDay())
            ->when($propertyId, fn($q) => $q->whereHas('room', fn($r) => $r->where('property_id', $propertyId)))
            ->with(['room.property', 'contract.tenant'])
            ->orderBy('due_date', 'asc')
            ->get();

        // 6. Cảnh báo sắp đến hạn (Due soon: trong 3 ngày tới)
        $dueSoonInvoices = Invoice::where('status', '!=', 'paid')
            ->whereDate('due_date', '>=', now()->startOfDay())
            ->whereDate('due_date', '<=', now()->addDays(3)->endOfDay())
            ->when($propertyId, fn($q) => $q->whereHas('room', fn($r) => $r->where('property_id', $propertyId)))
            ->with(['room.property', 'contract.tenant'])
            ->orderBy('due_date', 'asc')
            ->get();

        // 7. Cảnh báo hợp đồng sắp hết hạn (trong 30 ngày tới)
        $expiringContracts = Contract::where('status', '!=', 'terminated')
            ->whereDate('end_date', '>=', now()->startOfDay())
            ->whereDate('end_date', '<=', now()->addDays(30)->endOfDay())
            ->when($propertyId, fn($q) => $q->whereHas('room', fn($r) => $r->where('property_id', $propertyId)))
            ->with(['room.property', 'tenant'])
            ->orderBy('end_date', 'asc')
            ->get();

        // 8. Đối soát tiêu thụ Điện Nước (Đồng hồ tổng vs Tổng các phòng con)
        $totalSubRoomsElecKwh = $invoices->sum('electricity_usage');
        $totalMasterElecKwh = $expenses->where('expense_type', 'electricity_evn')->sum('total_meter_usage');
        $elecLeakKwh = max(0, $totalMasterElecKwh - $totalSubRoomsElecKwh);

        $totalSubRoomsWaterM3 = $invoices->sum('water_usage');
        $totalMasterWaterM3 = $expenses->where('expense_type', 'water_supply')->sum('total_meter_usage');
        $waterLeakM3 = max(0, $totalMasterWaterM3 - $totalSubRoomsWaterM3);

        // 9. Ý kiến / Khiếu nại hóa đơn từ khách thuê
        $feedbacks = InvoiceFeedback::with(['invoice.room.property', 'tenant'])
            ->when($propertyId, fn($q) => $q->whereHas('invoice.room', fn($r) => $r->where('property_id', $propertyId)))
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'properties',
            'propertyId',
            'month',
            'year',
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'maintenanceRooms',
            'occupancyRate',
            'totalInvoiceAmount',
            'totalCollected',
            'totalUnpaid',
            'totalExpenses',
            'expenseElecEvn',
            'expenseWaterSupply',
            'expenseStateTax',
            'expenseOther',
            'netProfit',
            'overdueInvoices',
            'dueSoonInvoices',
            'expiringContracts',
            'totalSubRoomsElecKwh',
            'totalMasterElecKwh',
            'elecLeakKwh',
            'totalSubRoomsWaterM3',
            'totalMasterWaterM3',
            'waterLeakM3',
            'feedbacks',
            'isManager'
        ));
    }
}
