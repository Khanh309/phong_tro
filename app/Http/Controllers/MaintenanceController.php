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
        $propertyId = $request->query('property_id');

        $properties = Property::all();

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
        $rooms = Room::with('property')->orderBy('property_id')->orderBy('room_number')->get();
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

        MaintenanceRequest::create($validated);

        return redirect()->route('maintenance.index')->with('success', 'Ghi nhận báo hỏng / sửa chữa thành công!');
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $rooms = Room::with('property')->get();
        return view('maintenance.edit', compact('maintenance', 'rooms'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
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

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')->with('success', 'Cập nhật sự cố thành công!');
    }

    public function destroy(MaintenanceRequest $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Đã xóa phiếu báo hỏng!');
    }
}
