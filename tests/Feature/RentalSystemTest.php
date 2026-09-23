<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RentalSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $this->actingAs($admin);
        }
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Bảng Điều Khiển Trung Tâm');
    }

    public function test_properties_page_renders_successfully(): void
    {
        $response = $this->get('/properties');
        $response->assertStatus(200);
        $response->assertSee('Chuỗi Cơ Sở / Nhà Trọ');
    }

    public function test_rooms_page_renders_successfully(): void
    {
        $response = $this->get('/rooms');
        $response->assertStatus(200);
        $response->assertSee('Danh Sách Phòng Trọ');
        $response->assertSee('Bảng Tổng Hợp Biểu Phí Tất Cả Các Phòng');
        $response->assertSee('Tiền mạng Internet Wifi');
        $response->assertSee('Phí vệ sinh');
    }

    public function test_vacant_finder_renders_successfully(): void
    {
        $response = $this->get('/rooms/vacant-finder');
        $response->assertStatus(200);
        $response->assertSee('Tra Cứu Phòng Trống Nhanh');
    }

    public function test_tenants_page_renders_successfully(): void
    {
        $response = $this->get('/tenants');
        $response->assertStatus(200);
        $response->assertSee('Hồ Sơ Khách Thuê Trọ');
    }

    public function test_police_report_renders_successfully(): void
    {
        $response = $this->get('/tenants/police-report');
        $response->assertStatus(200);
        $response->assertSee('DANH SÁCH KHÁCH THUÊ ĐĂNG KÝ TẠM TRÚ');
    }

    public function test_contracts_page_renders_successfully(): void
    {
        $response = $this->get('/contracts');
        $response->assertStatus(200);
        $response->assertSee('Hợp Đồng Thuê');
    }

    public function test_invoices_page_renders_successfully(): void
    {
        $response = $this->get('/invoices');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Hóa Đơn Phòng');
        $response->assertSee('Bảng Kê Chi Tiết Phí Từng Phòng Theo Tháng');
        $response->assertSee('Tất Cả Khách Thuê Trong Phòng');
        $response->assertSee('Trần Văn Nam');
        $response->assertSee('Wifi');
    }

    public function test_bulk_create_invoices_page_renders_successfully(): void
    {
        $response = $this->get('/invoices/bulk-create');
        $response->assertStatus(200);
        $response->assertSee('Chốt Số Điện Nước Cả Nhà Trọ');
    }

    public function test_expenses_page_renders_successfully(): void
    {
        $response = $this->get('/expenses');
        $response->assertStatus(200);
        $response->assertSee('Khoản Đóng Cho Nhà Nước');
    }

    public function test_financial_report_renders_successfully(): void
    {
        $response = $this->get('/reports/financial');
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo Tài Chính Thu - Chi');
    }

    public function test_maintenance_page_renders_successfully(): void
    {
        $response = $this->get('/maintenance');
        $response->assertStatus(200);
        $response->assertSee('Yêu Cầu Sửa Chữa');
    }

    public function test_tenant_portal_lookup_renders_successfully(): void
    {
        $response = $this->get('/tra-cuu');
        $response->assertStatus(200);
        $response->assertSee('Tra Cứu Tiền Phòng Trọ Online');
    }
}
