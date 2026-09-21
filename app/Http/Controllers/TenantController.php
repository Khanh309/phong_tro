<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Property;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $residence = $request->query('residence');

        $query = Tenant::with(['currentContract.room.property', 'contracts']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_card_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }

        if ($residence) {
            $query->where('temporary_residence_status', $residence);
        }

        $tenants = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('tenants.index', compact('tenants', 'search', 'residence'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'id_card_number' => 'nullable|string|max:25',
            'id_card_date' => 'nullable|date',
            'id_card_place' => 'nullable|string|max:150',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'hometown' => 'nullable|string|max:150',
            'vehicle_plate' => 'nullable|string|max:50',
            'temporary_residence_status' => 'required|in:not_registered,registered',
            'notes' => 'nullable|string',
        ]);

        $tenant = Tenant::create($validated);

        return redirect()->route('tenants.show', $tenant->id)->with('success', 'Thêm mới khách thuê thành công!');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['contracts.room.property', 'contracts.members']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'id_card_number' => 'nullable|string|max:25',
            'id_card_date' => 'nullable|date',
            'id_card_place' => 'nullable|string|max:150',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'hometown' => 'nullable|string|max:150',
            'vehicle_plate' => 'nullable|string|max:50',
            'temporary_residence_status' => 'required|in:not_registered,registered',
            'notes' => 'nullable|string',
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.show', $tenant->id)->with('success', 'Cập nhật thông tin khách thành công!');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->currentContract) {
            return back()->with('error', 'Không thể xóa khách đang có hợp đồng thuê phòng hiệu lực!');
        }

        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Đã xóa khách thuê!');
    }

    // Danh sách mẫu khai báo tạm trú gửi Công an
    public function policeRegistration(Request $request)
    {
        $propertyId = $request->query('property_id');
        $properties = Property::all();

        // Lấy tất cả khách đang ở (hợp đồng active) + thành viên ở cùng
        $query = Tenant::whereHas('currentContract', function ($q) use ($propertyId) {
            if ($propertyId) {
                $q->whereHas('room', fn($r) => $r->where('property_id', $propertyId));
            }
        })->with(['currentContract.room.property', 'currentContract.members']);

        $tenants = $query->get();

        return view('tenants.police_report', compact('tenants', 'properties', 'propertyId'));
    }
}
