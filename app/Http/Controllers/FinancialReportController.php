<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\PropertyExpense;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $propertyId = $request->query('property_id');
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $properties = Property::all();

        // 1. DÒNG THU (Từ khách thuê)
        $invoicesQuery = Invoice::with(['room.property', 'contract.tenant'])
            ->where('month', $month)
            ->where('year', $year);

        if ($propertyId) {
            $invoicesQuery->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
        }

        $invoices = $invoicesQuery->get();

        $revenueRoom = $invoices->sum('room_price');
        $revenueElec = $invoices->sum('electricity_total');
        $revenueWater = $invoices->sum('water_total');
        $revenueServices = $invoices->sum('other_fees');
        $totalInvoiced = $invoices->sum('total_amount');
        $totalCollected = $invoices->sum('paid_amount');
        $totalDebt = $invoices->sum('remaining_amount');

        // 2. DÒNG CHI (Trả cho Nhà nước & Nhà cung cấp)
        $expensesQuery = PropertyExpense::with('property')
            ->where('month', $month)
            ->where('year', $year);

        if ($propertyId) {
            $expensesQuery->where('property_id', $propertyId);
        }

        $expenses = $expensesQuery->get();

        $expenseElecEvn = $expenses->where('expense_type', 'electricity_evn')->sum('amount');
        $expenseWaterSupply = $expenses->where('expense_type', 'water_supply')->sum('amount');
        $expenseTax = $expenses->where('expense_type', 'state_tax')->sum('amount');
        $expenseInternet = $expenses->where('expense_type', 'internet_bill')->sum('amount');
        $expenseWaste = $expenses->where('expense_type', 'waste_collection')->sum('amount');
        $expenseMaintenance = $expenses->where('expense_type', 'maintenance_repair')->sum('amount');
        $expenseOther = $expenses->where('expense_type', 'other')->sum('amount');
        $totalExpenses = $expenses->sum('amount');

        // 3. LỢI NHUẬN RÒNG (NET PROFIT)
        $netProfitAccrual = $totalInvoiced - $totalExpenses; // Lợi nhuận theo phát sinh
        $netProfitCash = $totalCollected - $totalExpenses;    // Lợi nhuận theo dòng tiền thực thu

        // 4. ĐỐI SOÁT HAO HỤT ĐIỆN NƯỚC
        $subRoomsElecKwh = (float) $invoices->sum('electricity_usage');
        $masterElecKwh = (float) $expenses->where('expense_type', 'electricity_evn')->sum('total_meter_usage');
        $elecDiff = $masterElecKwh - $subRoomsElecKwh;
        $elecLossRate = $masterElecKwh > 0 ? round(($elecDiff / $masterElecKwh) * 100, 1) : 0;

        $subRoomsWaterM3 = (float) $invoices->sum('water_usage');
        $masterWaterM3 = (float) $expenses->where('expense_type', 'water_supply')->sum('total_meter_usage');
        $waterDiff = $masterWaterM3 - $subRoomsWaterM3;
        $waterLossRate = $masterWaterM3 > 0 ? round(($waterDiff / $masterWaterM3) * 100, 1) : 0;

        // 5. THỐNG KÊ CẢ NĂM (12 THÁNG)
        $yearlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            $mInvoices = Invoice::where('year', $year)->where('month', $m);
            $mExpenses = PropertyExpense::where('year', $year)->where('month', $m);

            if ($propertyId) {
                $mInvoices->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
                $mExpenses->where('property_id', $propertyId);
            }

            $rev = $mInvoices->sum('paid_amount');
            $exp = $mExpenses->sum('amount');
            $profit = $rev - $exp;

            $yearlyStats[] = [
                'month' => $m,
                'revenue' => $rev,
                'expense' => $exp,
                'profit' => $profit,
            ];
        }

        return view('reports.financial', compact(
            'properties',
            'propertyId',
            'month',
            'year',
            'revenueRoom',
            'revenueElec',
            'revenueWater',
            'revenueServices',
            'totalInvoiced',
            'totalCollected',
            'totalDebt',
            'expenseElecEvn',
            'expenseWaterSupply',
            'expenseTax',
            'expenseInternet',
            'expenseWaste',
            'expenseMaintenance',
            'expenseOther',
            'totalExpenses',
            'netProfitAccrual',
            'netProfitCash',
            'subRoomsElecKwh',
            'masterElecKwh',
            'elecDiff',
            'elecLossRate',
            'subRoomsWaterM3',
            'masterWaterM3',
            'waterDiff',
            'waterLossRate',
            'yearlyStats'
        ));
    }
}
