<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomFee;
use App\Models\RoomAsset;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
        }

        $status = $request->query('status');
        $floor = $request->query('floor');

        $roomsQuery = Room::with(['property', 'fees', 'assets', 'currentContract.tenant', 'currentContract.members', 'invoices' => function ($q) {
            $q->orderBy('year', 'desc')->orderBy('month', 'desc')->limit(1);
        }]);

        if ($propertyId) {
            $roomsQuery->where('property_id', $propertyId);
        }
        if ($status) {
            $roomsQuery->where('status', $status);
        }
        if ($floor) {
            $roomsQuery->where('floor', $floor);
        }

        $rooms = $roomsQuery->orderBy('floor')->orderBy('room_number')->get();

        // Nhóm theo tầng để hiển thị sơ đồ phòng
        $roomsByFloor = $rooms->groupBy('floor');

        return view('rooms.index', compact('rooms', 'properties', 'propertyId', 'status', 'floor', 'roomsByFloor'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $properties = Property::where('id', $user->property_id)->get();
            $selectedPropertyId = $user->property_id;
        } else {
            $properties = Property::all();
            $selectedPropertyId = $request->query('property_id');
        }

        $selectedProperty = $selectedPropertyId ? Property::find($selectedPropertyId) : $properties->first();
        return view('rooms.create', compact('properties', 'selectedPropertyId', 'selectedProperty'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:1',
            'max_tenants' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'electricity_meter_number' => 'nullable|string|max:50',
            'initial_electricity' => 'required|numeric|min:0',
            'electricity_rate' => 'required|numeric|min:0',
            'water_meter_number' => 'nullable|string|max:50',
            'initial_water' => 'required|numeric|min:0',
            'water_calculation_type' => 'required|in:meter,per_person,fixed_room',
            'water_rate' => 'required|numeric|min:0',
            'internet_type' => 'nullable|in:fixed,per_person,free',
            'internet_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();
        if ($user && !$user->isAdmin() && $validated['property_id'] != $user->property_id) {
            abort(403, 'Bạn chỉ được tạo phòng trong cơ sở được phân công.');
        }

        $property = Property::find($validated['property_id']);
        $validated['internet_type'] = $validated['internet_type'] ?? ($property?->default_internet_type ?? 'fixed');
        $validated['internet_rate'] = $validated['internet_rate'] ?? ($property?->default_internet_rate ?? 100000);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('rooms', 'public');
                $imagePaths[] = $path;
            }
        }
        $validated['images'] = $imagePaths;

        $room = Room::create($validated);

        // Tạo/cập nhật phí mạng theo cấu hình (fixed, per_person, free)
        if ($validated['internet_type'] !== 'free') {
            RoomFee::updateOrCreate(
                ['room_id' => $room->id, 'fee_name' => 'Tiền mạng Internet Wifi'],
                [
                    'fee_type' => $validated['internet_type'] === 'per_person' ? 'per_person' : 'fixed',
                    'unit_price' => $validated['internet_rate'],
                    'quantity' => 1,
                ]
            );
        }

        RoomFee::firstOrCreate(
            ['room_id' => $room->id, 'fee_name' => 'Tiền rác & Vệ sinh'],
            ['fee_type' => 'fixed', 'unit_price' => 40000, 'quantity' => 1]
        );

        return redirect()->route('rooms.show', $room->id)->with('success', 'Tạo phòng trọ mới thành công!');
    }

    public function show(Room $room)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xem phòng thuộc cơ sở khác.');
        }

        $room->load([
            'property',
            'fees',
            'assets',
            'contracts.tenant',
            'currentContract.members',
            'invoices' => fn($q) => $q->orderBy('year', 'desc')->orderBy('month', 'desc'),
            'maintenanceRequests' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            if ($room->property_id != $user->property_id) {
                abort(403, 'Bạn không có quyền chỉnh sửa phòng thuộc cơ sở khác.');
            }
            $properties = Property::where('id', $user->property_id)->get();
        } else {
            $properties = Property::all();
        }

        return view('rooms.edit', compact('room', 'properties'));
    }

    public function update(Request $request, Room $room)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            if ($room->property_id != $user->property_id) {
                abort(403, 'Bạn không có quyền chỉnh sửa phòng thuộc cơ sở khác.');
            }
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:1',
            'max_tenants' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'electricity_meter_number' => 'nullable|string|max:50',
            'initial_electricity' => 'required|numeric|min:0',
            'electricity_rate' => 'required|numeric|min:0',
            'water_meter_number' => 'nullable|string|max:50',
            'initial_water' => 'required|numeric|min:0',
            'water_calculation_type' => 'required|in:meter,per_person,fixed_room',
            'water_rate' => 'required|numeric|min:0',
            'internet_type' => 'nullable|in:fixed,per_person,free',
            'internet_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'string',
        ]);

        if ($user && !$user->isAdmin() && $validated['property_id'] != $user->property_id) {
            abort(403, 'Bạn không thể chuyển phòng sang cơ sở khác.');
        }

        $validated['internet_type'] = $validated['internet_type'] ?? ($room->internet_type ?? 'fixed');
        $validated['internet_rate'] = $validated['internet_rate'] ?? ($room->internet_rate ?? 100000);

        $currentImages = $room->images ?? [];

        // 1. Xóa các file ảnh được chọn xóa khỏi Storage
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            foreach ($request->delete_images as $imgToDelete) {
                if (in_array($imgToDelete, $currentImages)) {
                    Storage::disk('public')->delete($imgToDelete);
                    $currentImages = array_values(array_filter($currentImages, fn($img) => $img !== $imgToDelete));
                }
            }
        }

        // 2. Lưu thêm các file ảnh mới vào Storage
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('rooms', 'public');
                $currentImages[] = $path;
            }
        }

        $validated['images'] = $currentImages;

        $room->update($validated);

        // Đồng bộ phí mạng Wifi
        if ($validated['internet_type'] !== 'free') {
            RoomFee::updateOrCreate(
                ['room_id' => $room->id, 'fee_name' => 'Tiền mạng Internet Wifi'],
                [
                    'fee_type' => $validated['internet_type'] === 'per_person' ? 'per_person' : 'fixed',
                    'unit_price' => $validated['internet_rate'],
                    'quantity' => 1,
                ]
            );
        } else {
            RoomFee::where('room_id', $room->id)->where('fee_name', 'like', '%mạng%')->delete();
        }

        return redirect()->route('rooms.show', $room->id)->with('success', 'Cập nhật thông tin phòng thành công!');
    }

    public function destroy(Room $room)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa phòng thuộc cơ sở khác.');
        }

        if ($room->status === 'occupied') {
            return back()->with('error', 'Không thể xóa phòng đang có khách thuê!');
        }

        // Tự động xóa sạch toàn bộ file ảnh của phòng trong Storage
        if (!empty($room->images) && is_array($room->images)) {
            foreach ($room->images as $imgPath) {
                Storage::disk('public')->delete($imgPath);
            }
        }

        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Đã xóa phòng thành công!');
    }

    // Thêm phí riêng cho phòng
    public function addFee(Request $request, Room $room)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền thao tác với phòng thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'fee_name' => 'required|string|max:100',
            'fee_type' => 'required|in:fixed,per_person,per_unit',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $room->fees()->create($validated);
        return back()->with('success', 'Đã thêm khoản phí dịch vụ cho phòng!');
    }

    // Xóa phí riêng của phòng
    public function deleteFee(RoomFee $fee)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $fee->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa phí của phòng thuộc cơ sở khác.');
        }

        $fee->delete();
        return back()->with('success', 'Đã xóa khoản phí!');
    }

    // Thêm tài sản nội thất phòng
    public function addAsset(Request $request, Room $room)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền thao tác với phòng thuộc cơ sở khác.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        $room->assets()->create($validated);
        return back()->with('success', 'Đã thêm tài sản nội thất vào phòng!');
    }

    // Xóa tài sản phòng
    public function deleteAsset(RoomAsset $asset)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $asset->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa tài sản của phòng thuộc cơ sở khác.');
        }

        $asset->delete();
        return back()->with('success', 'Đã xóa tài sản khỏi phòng!');
    }

    // Tìm kiếm phòng trống nhanh (Quick Vacant Room Finder)
    public function vacantFinder(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
        }

        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $minArea = $request->query('min_area');

        $query = Room::where('status', 'available')->with('property');

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        if ($minArea) {
            $query->where('area', '>=', $minArea);
        }

        $vacantRooms = $query->orderBy('price', 'asc')->get();

        return view('rooms.vacant_finder', compact('vacantRooms', 'properties', 'propertyId', 'minPrice', 'maxPrice', 'minArea'));
    }
}
