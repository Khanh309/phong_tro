<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PropertyExpenseController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\TenantPortalController;

/*
|--------------------------------------------------------------------------
| Web Routes - Hệ thống Quản lý Nhà trọ & Căn hộ cho thuê
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. XÁC THỰC TÀI KHOẢN (AUTHENTICATION)
// ==========================================
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:30,1');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. KHU VỰC QUẢN TRỊ (YÊU CẦU ĐĂNG NHẬP: AUTH)
// ==========================================
Route::middleware(['auth', 'role.staff'])->group(function () {

    // DASHBOARD & TỔNG QUAN
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // PHÒNG TRỌ & DỊCH VỤ ĐI KÈM
    Route::get('rooms/vacant-finder', [RoomController::class, 'vacantFinder'])->name('rooms.vacant_finder');
    Route::post('rooms/{room}/fees', [RoomController::class, 'addFee'])->name('rooms.fees.store');
    Route::delete('rooms/fees/{fee}', [RoomController::class, 'deleteFee'])->name('rooms.fees.destroy');
    Route::post('rooms/{room}/assets', [RoomController::class, 'addAsset'])->name('rooms.assets.store');
    Route::delete('rooms/assets/{asset}', [RoomController::class, 'deleteAsset'])->name('rooms.assets.destroy');
    Route::resource('rooms', RoomController::class);

    // KHÁCH THUÊ & KHAI BÁO TẠM TRÚ
    Route::get('tenants/police-report', [TenantController::class, 'policeRegistration'])->name('tenants.police_report');
    Route::post('tenants/{tenant}/account', [TenantController::class, 'createAccount'])->name('tenants.account.create');
    Route::post('tenants/{tenant}/account/reset-password', [TenantController::class, 'resetPassword'])->name('tenants.account.reset_password');
    Route::delete('tenants/{tenant}/account', [TenantController::class, 'deleteAccount'])->name('tenants.account.destroy');
    Route::resource('tenants', TenantController::class);

    // HỢP ĐỒNG & QUY TRÌNH TRẢ PHÒNG (CHECK-OUT)
    Route::post('contracts/{contract}/members', [ContractController::class, 'addMember'])->name('contracts.members.store');
    Route::delete('contracts/members/{member}', [ContractController::class, 'deleteMember'])->name('contracts.members.destroy');
    Route::get('contracts/{contract}/print', [ContractController::class, 'print'])->name('contracts.print');
    Route::get('contracts/{contract}/checkout', [ContractController::class, 'showCheckout'])->name('contracts.checkout');
    Route::post('contracts/{contract}/checkout', [ContractController::class, 'processCheckout'])->name('contracts.checkout.process');
    Route::resource('contracts', ContractController::class);

    // ĐIỆN NƯỚC & HÓA ĐƠN HÀNG THÁNG
    Route::get('invoices/bulk-create', [InvoiceController::class, 'bulkCreate'])->name('invoices.bulk_create');
    Route::post('invoices/bulk-store', [InvoiceController::class, 'bulkStore'])->name('invoices.bulk_store');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
    Route::resource('invoices', InvoiceController::class);

    // PHẢN HỒI KHIẾU NẠI CỦA KHÁCH THUÊ
    Route::post('admin/feedbacks/{feedback}/reply', [TenantPortalController::class, 'replyFeedback'])->name('feedbacks.reply');

    // QUẢN LÝ SỰ CỐ & BÁO HỎNG ĐỒ ĐẠC
    Route::resource('maintenance', MaintenanceController::class);

    // ==========================================
    // 3. KHU VỰC DÀNH RIÊNG CHO CHỦ TRỌ (ADMIN ONLY)
    // ==========================================
    Route::middleware(['role.admin'])->group(function () {
        // Thêm, sửa, xóa cơ sở / chuỗi nhà trọ (Multi-property)
        Route::get('properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

        // Chi phí đầu ra trả cho Nhà nước & Vận hành (Điện EVN, Nước tổng, Thuế)
        Route::resource('expenses', PropertyExpenseController::class);

        // Báo cáo Tài chính Lợi nhuận Ròng & Thất thoát điện nước
        Route::get('reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
    });

    // XEM CƠ SỞ / NHÀ TRỌ (Admin toàn quyền, Quản lý xem cơ sở phụ trách)
    Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
});

// ==========================================
// 4. CỔNG KHÁCH THUÊ TRỌ (TENANT PORTAL - CÔNG KHAI)
// ==========================================
Route::get('khach-thue', [TenantPortalController::class, 'index'])->name('portal.index');
Route::get('tra-cuu', [TenantPortalController::class, 'index'])->name('portal.lookup');
Route::post('khach-thue/select', [TenantPortalController::class, 'selectTenant'])->name('portal.select');
Route::post('khach-thue/logout', [TenantPortalController::class, 'logoutTenant'])->name('portal.logout');
Route::post('khach-thue/feedback/{invoice}', [TenantPortalController::class, 'storeFeedback'])->name('portal.feedback.store');
Route::post('khach-thue/maintenance', [TenantPortalController::class, 'storeMaintenance'])->name('portal.maintenance.store');
Route::get('tra-cuu/{code}', [TenantPortalController::class, 'show'])->name('portal.show');
