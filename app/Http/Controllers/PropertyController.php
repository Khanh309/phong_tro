<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

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
            'default_water_type' => 'nullable|in:meter,per_person,fixed_room',
            'default_water_rate' => 'nullable|numeric|min:0',
            'default_internet_type' => 'nullable|in:fixed,per_person,free',
            'default_internet_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $validated['default_water_type'] = $validated['default_water_type'] ?? 'meter';
        $validated['default_water_rate'] = $validated['default_water_rate'] ?? 30000;
        $validated['default_internet_type'] = $validated['default_internet_type'] ?? 'fixed';
        $validated['default_internet_rate'] = $validated['default_internet_rate'] ?? 100000;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('properties', 'public');
        }

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
            'default_water_type' => 'nullable|in:meter,per_person,fixed_room',
            'default_water_rate' => 'nullable|numeric|min:0',
            'default_internet_type' => 'nullable|in:fixed,per_person,free',
            'default_internet_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $validated['default_water_type'] = $validated['default_water_type'] ?? ($property->default_water_type ?? 'meter');
        $validated['default_water_rate'] = $validated['default_water_rate'] ?? ($property->default_water_rate ?? 30000);
        $validated['default_internet_type'] = $validated['default_internet_type'] ?? ($property->default_internet_type ?? 'fixed');
        $validated['default_internet_rate'] = $validated['default_internet_rate'] ?? ($property->default_internet_rate ?? 100000);

        if ($request->boolean('remove_image')) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $validated['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $validated['image'] = $request->file('image')->store('properties', 'public');
        }

        $property->update($validated);

        return redirect()->route('properties.show', $property->id)->with('success', 'Cập nhật thông tin nhà trọ thành công!');
    }

    public function destroy(Property $property)
    {
        if ($property->rooms()->count() > 0) {
            return back()->with('error', 'Không thể xóa nhà trọ đang có phòng trọ! Vui lòng xóa hết các phòng trước.');
        }

        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        $property->delete();
        return redirect()->route('properties.index')->with('success', 'Đã xóa nhà trọ thành công!');
    }
}
