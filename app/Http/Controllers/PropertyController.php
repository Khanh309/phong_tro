<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Property::withCount(['rooms', 'rooms as occupied_rooms_count' => function ($q) {
            $q->where('status', 'occupied');
        }, 'rooms as available_rooms_count' => function ($q) {
            $q->where('status', 'available');
        }]);

        if ($user && !$user->isAdmin()) {
            $query->where('id', $user->property_id);
        }

        $properties = $query->get();

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'total_floors' => 'required|integer|min:1',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:100',
            'electricity_meter_code' => 'nullable|string|max:50',
            'water_meter_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $property = Property::create($validated);

        return redirect()->route('properties.show', $property->id)->with('success', 'Thêm mới nhà trọ thành công!');
    }

    public function show(Property $property, Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $user->property_id != $property->id) {
            abort(403, 'Bạn không có quyền truy cập cơ sở nhà trọ này.');
        }

        $property->load(['rooms' => function ($q) {
            $q->with(['fees', 'assets', 'currentContract.tenant', 'currentContract.members'])->orderBy('floor')->orderBy('room_number');
        }, 'expenses' => function ($q) {
            $q->orderBy('year', 'desc')->orderBy('month', 'desc');
        }]);

        return view('properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'total_floors' => 'required|integer|min:1',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:100',
            'electricity_meter_code' => 'nullable|string|max:50',
            'water_meter_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $property->update($validated);

        return redirect()->route('properties.show', $property->id)->with('success', 'Cập nhật thông tin nhà trọ thành công!');
    }

    public function destroy(Property $property)
    {
        if ($property->rooms()->count() > 0) {
            return back()->with('error', 'Không thể xóa nhà trọ đang có phòng trọ! Vui lòng xóa hết các phòng trước.');
        }

        $property->delete();
        return redirect()->route('properties.index')->with('success', 'Đã xóa nhà trọ thành công!');
    }
}
