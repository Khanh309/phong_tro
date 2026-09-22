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

        $query = Tenant::with(['currentContract.room.property', 'contracts', 'user']);

        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('contracts.room', fn($r) => $r->where('property_id', $user->property_id))
                  ->orWhereDoesntHave('contracts');
            });
        }

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

    protected function authorizeTenantAccess(Tenant $tenant): void
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            $hasAnyContract = $tenant->contracts()->exists();
            $hasContractInProperty = $tenant->contracts()->whereHas('room', fn($r) => $r->where('property_id', $user->property_id))->exists();
            if ($hasAnyContract && !$hasContractInProperty) {
                abort(403, 'Bạn không có quyền thao tác với khách thuê thuộc cơ sở khác.');
            }
        }
    }

    public function show(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        $tenant->load(['contracts.room.property', 'contracts.members', 'user']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeTenantAccess($tenant);

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
        $this->authorizeTenantAccess($tenant);

        if ($tenant->currentContract) {
            return back()->with('error', 'Không thể xóa khách đang có hợp đồng thuê phòng hiệu lực!');
        }

        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Đã xóa khách thuê!');
    }

    // Danh sách mẫu khai báo tạm trú gửi Công an
    public function policeRegistration(Request $request)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin()) {
            $propertyId = $user->property_id;
            $properties = Property::where('id', $propertyId)->get();
        } else {
            $propertyId = $request->query('property_id');
            $properties = Property::all();
        }

        // Lấy tất cả khách đang ở (hợp đồng active) + thành viên ở cùng
        $query = Tenant::whereHas('currentContract', function ($q) use ($propertyId) {
            if ($propertyId) {
                $q->whereHas('room', fn($r) => $r->where('property_id', $propertyId));
            }
        })->with(['currentContract.room.property', 'currentContract.members']);

        $tenants = $query->get();

        return view('tenants.police_report', compact('tenants', 'properties', 'propertyId'));
    }

    // Cấp tài khoản đăng nhập cho khách thuê (Chỉ Admin)
    public function createAccount(Request $request, Tenant $tenant)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới có quyền cấp tài khoản đăng nhập cho khách thuê.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        \App\Models\User::create([
            'name' => $tenant->name,
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'tenant',
            'tenant_id' => $tenant->id,
        ]);

        if (!$tenant->email) {
            $tenant->update(['email' => $validated['email']]);
        }

        return back()->with('success', "Đã cấp tài khoản đăng nhập thành công cho khách thuê {$tenant->name} ({$validated['email']})!");
    }

    // Đổi mật khẩu tài khoản khách thuê (Chỉ Admin)
    public function resetPassword(Request $request, Tenant $tenant)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới có quyền đổi mật khẩu tài khoản khách thuê.');
        }

        $user = \App\Models\User::where('tenant_id', $tenant->id)->firstOrFail();
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('success', "Đã đổi mật khẩu thành công cho tài khoản {$user->email}!");
    }

    // Hủy tài khoản đăng nhập của khách (Chỉ Admin)
    public function deleteAccount(Tenant $tenant)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới có quyền hủy tài khoản đăng nhập của khách thuê.');
        }

        $user = \App\Models\User::where('tenant_id', $tenant->id)->first();
        if ($user) {
            $user->delete();
        }

        return back()->with('success', "Đã hủy tài khoản đăng nhập của khách thuê {$tenant->name}!");
    }
}
