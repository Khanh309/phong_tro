<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyExpense;
use App\Models\Property;

class PropertyExpenseController extends Controller
{
    public function index(Request $request)
    {
        $propertyId = $request->query('property_id');
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $type = $request->query('type');

        $properties = Property::all();

        $query = PropertyExpense::with('property')
            ->where('month', $month)
            ->where('year', $year);

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($type) {
            $query->where('expense_type', $type);
        }

        $expenses = $query->orderBy('payment_date', 'desc')->get();

        // Tổng chi phí
        $totalExpenses = $expenses->sum('amount');
        $taxTotal = $expenses->where('expense_type', 'state_tax')->sum('amount');
        $evnTotal = $expenses->where('expense_type', 'electricity_evn')->sum('amount');
        $waterTotal = $expenses->where('expense_type', 'water_supply')->sum('amount');
        $operationTotal = $totalExpenses - ($taxTotal + $evnTotal + $waterTotal);

        return view('expenses.index', compact(
            'expenses',
            'properties',
            'propertyId',
            'month',
            'year',
            'type',
            'totalExpenses',
            'taxTotal',
            'evnTotal',
            'waterTotal',
            'operationTotal'
        ));
    }

    public function create(Request $request)
    {
        $properties = Property::all();
        $selectedPropertyId = $request->query('property_id');
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        return view('expenses.create', compact('properties', 'selectedPropertyId', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2050',
            'expense_type' => 'required|in:electricity_evn,water_supply,state_tax,internet_bill,waste_collection,maintenance_repair,other',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'total_meter_usage' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'paid_by' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        PropertyExpense::create($validated);

        return redirect()->route('expenses.index', [
            'property_id' => $validated['property_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('success', 'Ghi nhận chi phí nhà trọ thành công!');
    }

    public function edit(PropertyExpense $expense)
    {
        $properties = Property::all();
        return view('expenses.edit', compact('expense', 'properties'));
    }

    public function update(Request $request, PropertyExpense $expense)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2050',
            'expense_type' => 'required|in:electricity_evn,water_supply,state_tax,internet_bill,waste_collection,maintenance_repair,other',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'total_meter_usage' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'paid_by' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index', [
            'property_id' => $expense->property_id,
            'month' => $expense->month,
            'year' => $expense->year,
        ])->with('success', 'Cập nhật chi phí thành công!');
    }

    public function destroy(PropertyExpense $expense)
    {
        $propertyId = $expense->property_id;
        $month = $expense->month;
        $year = $expense->year;

        $expense->delete();

        return redirect()->route('expenses.index', [
            'property_id' => $propertyId,
            'month' => $month,
            'year' => $year,
        ])->with('success', 'Đã xóa khoản chi phí!');
    }
}
