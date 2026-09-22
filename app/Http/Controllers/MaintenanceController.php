<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Property;

class MaintenanceController extends Controller
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

        $query = MaintenanceRequest::with(['room.property']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($propertyId) {
            $query->whereHas('room', fn($q) => $q->where('property_id', $propertyId));
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('maintenance.index', compact('requests', 'properties', 'status', 'propertyId'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $roomsQuery = Room::with('property')->orderBy('property_id')->orderBy('room_number');
        if ($user && !$user->isAdmin()) {
            $roomsQuery->where('property_id', $user->property_id);
        }
        $rooms = $roomsQuery->get();
        $selectedRoomId = $request->query('room_id');
        return view('maintenance.create', compact('rooms', 'selectedRoomId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed',
            'reported_date' => 'required|date',
            'resolved_date' => 'nullable|date',
        ]);

        $user = $request->user();
        $room = Room::findOrFail($validated['room_id']);
        if ($user && !$user->isAdmin() && $room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền báo hỏng cho phòng thuộc cơ sở khác.');
        }

        MaintenanceRequest::create($validated);

        return redirect()->route('maintenance.index')->with('success', 'Ghi nhận báo hỏng / sửa chữa thành công!');
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $maintenance->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa sự cố thuộc cơ sở khác.');
        }

        $roomsQuery = Room::with('property');
        if ($user && !$user->isAdmin()) {
            $roomsQuery->where('property_id', $user->property_id);
        }
        $rooms = $roomsQuery->get();

        return view('maintenance.edit', compact('maintenance', 'rooms'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            if ($maintenance->room->property_id != $user->property_id) {
                abort(403, 'Bạn không có quyền chỉnh sửa sự cố thuộc cơ sở khác.');
            }
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed',
            'reported_date' => 'required|date',
            'resolved_date' => 'nullable|date',
        ]);

        if ($user && !$user->isAdmin()) {
            $targetRoom = Room::findOrFail($validated['room_id']);
            if ($targetRoom->property_id != $user->property_id) {
                abort(403, 'Bạn không thể chuyển sự cố sang phòng thuộc cơ sở khác.');
            }
        }

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')->with('success', 'Cập nhật sự cố thành công!');
    }

    public function destroy(MaintenanceRequest $maintenance)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin() && $maintenance->room->property_id != $user->property_id) {
            abort(403, 'Bạn không có quyền xóa sự cố thuộc cơ sở khác.');
        }

        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Đã xóa phiếu báo hỏng!');
    }
}
