<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\InvoiceFeedback;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantFeedbackAndPortalTest extends TestCase
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

    public function test_dashboard_displays_invoice_code_prominently(): void
    {
        $overdueInvoice = Invoice::where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now()->startOfDay())
            ->first();

        $this->assertNotNull($overdueInvoice);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($overdueInvoice->invoice_code);
    }

    public function test_invoices_index_displays_invoice_code(): void
    {
        $invoice = Invoice::first();
        $this->assertNotNull($invoice);

        $response = $this->get('/invoices');
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_code);
    }

    public function test_tenant_can_view_portal_with_categorized_invoices(): void
    {
        $tenant = Tenant::first();
        $this->assertNotNull($tenant);

        // Login as tenant via session
        $response = $this->withSession(['tenant_id' => $tenant->id])->get('/khach-thue');
        $response->assertStatus(200);
        $response->assertSee($tenant->name);
        $response->assertSee('Đang Chờ Nộp');
        $response->assertSee('Quá Hạn Nộp');
        $response->assertSee('Đã Thanh Toán');
        $response->assertSee('Biểu Phí & Dịch Vụ', false);
        $response->assertSee('Tiền mạng Internet Wifi');
    }

    public function test_tenant_can_submit_feedback_and_landlord_can_reply(): void
    {
        $tenant = Tenant::first();
        $invoice = Invoice::where('status', '!=', 'paid')->first();

        $this->assertNotNull($tenant);
        $this->assertNotNull($invoice);

        // 1. Tenant gửi khiếu nại chỉ số điện
        $response = $this->withSession(['tenant_id' => $tenant->id])
            ->post("/khach-thue/feedback/{$invoice->id}", [
                'feedback_type' => 'electricity',
                'content' => 'Số điện tháng này nhảy vọt bất thường, nhờ chủ nhà kiểm tra lại công tơ giúp em.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $feedback = InvoiceFeedback::where('invoice_id', $invoice->id)
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->first();

        $this->assertNotNull($feedback);
        $this->assertEquals('pending', $feedback->status);
        $this->assertEquals('electricity', $feedback->feedback_type);

        // 2. Chủ nhà phản hồi khiếu nại
        $replyResponse = $this->post("/admin/feedbacks/{$feedback->id}/reply", [
            'status' => 'resolved',
            'admin_reply' => 'Chào em, chủ nhà đã cho thợ điện sang kiểm tra công tơ số 101, kết quả bình thường do tháng này dùng điều hòa nhiều hơn em nhé.',
        ]);

        $replyResponse->assertRedirect();
        $replyResponse->assertSessionHas('success');

        $feedback->refresh();
        $this->assertEquals('resolved', $feedback->status);
        $this->assertNotNull($feedback->resolved_at);
        $this->assertStringContainsString('chủ nhà đã cho thợ điện', $feedback->admin_reply);
    }

    public function test_tenant_can_report_maintenance_issue(): void
    {
        $tenant = Tenant::has('currentContract')->first();
        $this->assertNotNull($tenant);

        $response = $this->withSession(['tenant_id' => $tenant->id])
            ->post('/khach-thue/maintenance', [
                'title' => 'Bóng đèn hành lang bị cháy',
                'description' => 'Bóng tuýp trước cửa phòng bị nhấp nháy rồi tắt hẳn tối qua.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('maintenance_requests', [
            'room_id' => $tenant->currentContract->room_id,
            'title' => 'Bóng đèn hành lang bị cháy',
            'status' => 'pending',
        ]);
    }

    public function test_tenant_invoice_detail_is_isolated_and_linked_correctly(): void
    {
        $tenant = Tenant::first();
        $invoice = Invoice::first();
        $this->assertNotNull($tenant);
        $this->assertNotNull($invoice);

        // Tenant dashboard should link to /tra-cuu/{code} (portal.show), NOT /invoices/{id}
        $dashboardRes = $this->withSession(['tenant_id' => $tenant->id])->get('/khach-thue');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee(route('portal.show', $invoice->invoice_code));
        $dashboardRes->assertDontSee(route('invoices.show', $invoice->id));

        // Unauthenticated guest or tenant can view invoice detail via portal.show without login redirect
        // Logging out of admin
        auth()->logout();
        $detailRes = $this->get(route('portal.show', $invoice->invoice_code));
        $detailRes->assertStatus(200);
        $detailRes->assertSee($invoice->invoice_code);
        $detailRes->assertSee('Quét Mã VietQR');
        $detailRes->assertSee('Khiếu Nại / Góp Ý Về Hóa Đơn Này');
        // Must not contain admin actions
        $detailRes->assertDontSee('Xác Nhận Đã Thu Tiền');
        $detailRes->assertDontSee('Nhắc Nợ Zalo');
    }
}
