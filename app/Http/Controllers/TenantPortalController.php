<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\MaintenanceRequest;
use App\Models\InvoiceFeedback;

class TenantPortalController extends Controller
{
    // CỔNG DÀNH CHO KHÁCH THUÊ TRỌ
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');

        // Nếu đã đăng nhập tài khoản khách thuê qua Auth
        if (!$tenantId && auth()->check() && auth()->user()->isTenant()) {
            $tenantId = auth()->user()->tenant_id;
            if ($tenantId) {
                session(['tenant_id' => $tenantId]);
            }
        }

        $phone = $request->query('phone');

        // Nếu có truyền SĐT qua query hoặc form
        if ($phone) {
            $found = Tenant::where('phone', trim($phone))->first();
            if ($found) {
                // Nếu khách thuê đã có tài khoản User (đã đặt mật khẩu) và chưa đăng nhập, bắt buộc chuyển sang trang /login
                if ($found->user()->exists() && (!auth()->check() || auth()->user()->tenant_id !== $found->id)) {
                    return redirect()->route('login')
                        ->with('info', "Khách thuê {$found->name} đã được cấp tài khoản hệ thống. Vui lòng đăng nhập bằng Email và Mật khẩu để bảo mật thông tin.");
                }
                session(['tenant_id' => $found->id]);
                $tenantId = $found->id;
            } else {
                return redirect()->route('portal.index')->with('error', 'Không tìm thấy thông tin khách thuê với số điện thoại này.');
            }
        }

        // Danh sách khách thuê để chọn nhanh (chỉ tải ở môi trường demo)
        $allTenants = (config('app.env') !== 'production' || config('app.debug'))
            ? Tenant::whereHas('currentContract')->with('currentContract.room.property')->get()
            : collect();

        if (!$tenantId) {
            return view('portal.login', compact('allTenants'));
        }

        $tenant = Tenant::with(['currentContract.room.property', 'currentContract.room.fees', 'currentContract.room.assets', 'currentContract.members'])->find($tenantId);
        if (!$tenant) {
            session()->forget('tenant_id');
            return redirect()->route('portal.index');
        }

        // 1. Phân loại 3 nhóm hóa đơn
        $room = $tenant->currentContract?->room;
        $invoicesQuery = Invoice::where('room_id', $room?->id ?? 0)
            ->with(['room.property', 'feedbacks'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        $allInvoices = $invoicesQuery->get();

        // 🔴 Hóa đơn quá hạn
        $overdueInvoices = $allInvoices->filter(function ($inv) {
            return in_array($inv->status, ['unpaid', 'partially_paid']) && now()->startOfDay()->gt($inv->due_date);
        });

        // ⏳ Hóa đơn đang đợi thanh toán (chưa quá hạn)
        $pendingInvoices = $allInvoices->filter(function ($inv) {
            return in_array($inv->status, ['unpaid', 'partially_paid']) && now()->startOfDay()->lte($inv->due_date);
        });

        // 🟢 Hóa đơn đã thanh toán
        $paidInvoices = $allInvoices->where('status', 'paid');

        // 2. Danh sách sự cố / báo hỏng của phòng khách
        $maintenanceRequests = $room ? MaintenanceRequest::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        // 3. Lịch sử ý kiến / khiếu nại hóa đơn của khách
        $myFeedbacks = InvoiceFeedback::where('tenant_id', $tenant->id)
            ->with('invoice')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portal.dashboard', compact(
            'tenant',
            'room',
            'allTenants',
            'overdueInvoices',
            'pendingInvoices',
            'paidInvoices',
            'maintenanceRequests',
            'myFeedbacks'
        ));
    }

    // Chọn nhanh khách thuê (Chỉ cho phép ở môi trường demo / dev)
    public function selectTenant(Request $request)
    {
        if (config('app.env') === 'production' && !config('app.debug')) {
            abort(403, 'Tính năng chọn nhanh chỉ hỗ trợ trong môi trường thử nghiệm.');
        }

        $tenantId = $request->input('tenant_id');
        if ($tenantId) {
            session(['tenant_id' => $tenantId]);
        }
        return redirect()->route('portal.index');
    }

    // Thoát tài khoản khách thuê
    public function logoutTenant()
    {
        session()->forget('tenant_id');
        if (auth()->check() && auth()->user()->isTenant()) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        return redirect()->route('portal.index');
    }

    // Khách thuê gửi khiếu nại / ý kiến về hóa đơn
    public function storeFeedback(Request $request, Invoice $invoice)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId && auth()->check() && auth()->user()->isTenant()) {
            $tenantId = auth()->user()->tenant_id;
        }

        if (!$tenantId) {
            return back()->with('error', 'Vui lòng chọn khách thuê trước khi gửi khiếu nại!');
        }

        // Kiểm tra xem hóa đơn này có thuộc về khách thuê hiện tại không
        $isTenantInvoice = ($invoice->contract && $invoice->contract->tenant_id == $tenantId)
            || ($invoice->room->currentContract && $invoice->room->currentContract->tenant_id == $tenantId);

        if (!$isTenantInvoice) {
            abort(403, 'Bạn không thể gửi khiếu nại cho hóa đơn của phòng khác.');
        }

        $validated = $request->validate([
            'feedback_type' => 'required|in:electricity,water,fees,other',
            'content' => 'required|string|min:5|max:1000',
        ]);

        InvoiceFeedback::create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenantId,
            'feedback_type' => $validated['feedback_type'],
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Đã gửi ý kiến / khiếu nại về hóa đơn! Ban quản lý sẽ kiểm tra và phản hồi sớm.');
    }

    // Khách thuê báo hỏng thiết bị phòng
    public function storeMaintenance(Request $request)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId && auth()->check() && auth()->user()->isTenant()) {
            $tenantId = auth()->user()->tenant_id;
        }

        $tenant = Tenant::with('currentContract.room')->find($tenantId);

        if (!$tenant || !$tenant->currentContract) {
            return back()->with('error', 'Bạn chưa được gán phòng nào!');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        MaintenanceRequest::create([
            'room_id' => $tenant->currentContract->room_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
            'reported_date' => now()->toDateString(),
        ]);

        return back()->with('success', 'Đã gửi báo hỏng thiết bị! Chủ nhà sẽ liên hệ thợ sửa chữa sớm nhất.');
    }

    // Chủ nhà phản hồi khiếu nại
    public function replyFeedback(Request $request, InvoiceFeedback $feedback)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            if ($feedback->invoice->room->property_id != $user->property_id) {
                abort(403, 'Bạn không có quyền phản hồi khiếu nại của cơ sở khác.');
            }
        }

        $validated = $request->validate([
            'admin_reply' => 'required|string',
            'status' => 'required|in:processing,resolved,rejected',
        ]);

        $feedback->update([
            'admin_reply' => $validated['admin_reply'],
            'status' => $validated['status'],
            'resolved_at' => in_array($validated['status'], ['resolved', 'rejected']) ? now() : null,
        ]);

        return back()->with('success', 'Đã lưu phản hồi khiếu nại cho khách thuê!');
    }

    // Cổng tra cứu nhanh theo mã hóa đơn
    public function show(string $code)
    {
        $invoice = Invoice::where('invoice_code', $code)
            ->with(['room.property', 'contract.tenant', 'feedbacks'])
            ->firstOrFail();

        // Nếu đã đăng nhập tài khoản khách thuê hoặc có session khách thuê
        $activeTenantId = (auth()->check() && auth()->user()->isTenant()) ? auth()->user()->tenant_id : session('tenant_id');
        if ($activeTenantId && (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isManager()))) {
            $isOwnInvoice = ($invoice->contract && $invoice->contract->tenant_id == $activeTenantId)
                || ($invoice->room->currentContract && $invoice->room->currentContract->tenant_id == $activeTenantId);
            if (!$isOwnInvoice) {
                abort(403, 'Bạn không có quyền xem hóa đơn của khách thuê khác.');
            }
        }

        $room = $invoice->room;
        $tenant = $invoice->contract?->tenant ?? Tenant::find(session('tenant_id'));

        return view('portal.invoice_detail', compact('invoice', 'room', 'tenant'));
    }
}
