<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\InvoiceFeedback;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductionSecurityAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $managerA;
    protected User $managerB;
    protected Property $propertyA;
    protected Property $propertyB;
    protected Room $roomA;
    protected Room $roomB;
    protected Tenant $tenantA;
    protected Tenant $tenantB;
    protected User $tenantUserA;
    protected User $tenantUserB;
    protected Contract $contractA;
    protected Contract $contractB;
    protected Invoice $invoiceA;
    protected Invoice $invoiceB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@nhatro.vn'],
            ['name' => 'Admin Test', 'password' => bcrypt('123456'), 'role' => 'admin']
        );

        $this->propertyA = Property::firstOrCreate(
            ['name' => 'Cơ sở Alpha Test'],
            ['address' => '123 Alpha St', 'city' => 'Hà Nội', 'total_floors' => 3]
        );

        $this->propertyB = Property::firstOrCreate(
            ['name' => 'Cơ sở Beta Test'],
            ['address' => '456 Beta St', 'city' => 'Hà Nội', 'total_floors' => 3]
        );

        $this->managerA = User::firstOrCreate(
            ['email' => 'manager_a_test@nhatro.vn'],
            ['name' => 'Manager A Test', 'password' => bcrypt('123456'), 'role' => 'manager', 'property_id' => $this->propertyA->id]
        );
        $this->managerA->update(['role' => 'manager', 'property_id' => $this->propertyA->id]);

        $this->managerB = User::firstOrCreate(
            ['email' => 'manager_b_test@nhatro.vn'],
            ['name' => 'Manager B Test', 'password' => bcrypt('123456'), 'role' => 'manager', 'property_id' => $this->propertyB->id]
        );
        $this->managerB->update(['role' => 'manager', 'property_id' => $this->propertyB->id]);

        $this->roomA = Room::firstOrCreate(
            ['property_id' => $this->propertyA->id, 'room_number' => 'TEST-101'],
            ['price' => 3000000, 'area' => 20, 'status' => 'occupied', 'electricity_rate' => 3500, 'water_rate' => 30000]
        );

        $this->roomB = Room::firstOrCreate(
            ['property_id' => $this->propertyB->id, 'room_number' => 'TEST-201'],
            ['price' => 3500000, 'area' => 25, 'status' => 'occupied', 'electricity_rate' => 3500, 'water_rate' => 30000]
        );

        $this->tenantA = Tenant::firstOrCreate(
            ['phone' => '0901111111'],
            ['name' => 'Khách Thuê A', 'gender' => 'Nam', 'temporary_residence_status' => 'registered']
        );

        $this->tenantB = Tenant::firstOrCreate(
            ['phone' => '0902222222'],
            ['name' => 'Khách Thuê B', 'gender' => 'Nữ', 'temporary_residence_status' => 'registered']
        );

        $this->tenantUserA = User::firstOrCreate(
            ['email' => 'tenant_a_test@nhatro.vn'],
            ['name' => 'Khách A User', 'password' => bcrypt('123456'), 'role' => 'tenant', 'tenant_id' => $this->tenantA->id]
        );

        $this->tenantUserB = User::firstOrCreate(
            ['email' => 'tenant_b_test@nhatro.vn'],
            ['name' => 'Khách B User', 'password' => bcrypt('123456'), 'role' => 'tenant', 'tenant_id' => $this->tenantB->id]
        );

        $this->contractA = Contract::firstOrCreate(
            ['contract_code' => 'HD-TEST-ALPHA'],
            [
                'room_id' => $this->roomA->id,
                'tenant_id' => $this->tenantA->id,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(10)->toDateString(),
                'rental_price' => 3000000,
                'deposit_amount' => 3000000,
                'deposit_status' => 'held',
                'status' => 'active'
            ]
        );

        $this->contractB = Contract::firstOrCreate(
            ['contract_code' => 'HD-TEST-BETA'],
            [
                'room_id' => $this->roomB->id,
                'tenant_id' => $this->tenantB->id,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(10)->toDateString(),
                'rental_price' => 3500000,
                'deposit_amount' => 3500000,
                'deposit_status' => 'held',
                'status' => 'active'
            ]
        );

        $this->invoiceA = Invoice::firstOrCreate(
            ['invoice_code' => 'INV-TEST-A'],
            [
                'contract_id' => $this->contractA->id,
                'room_id' => $this->roomA->id,
                'month' => now()->month,
                'year' => now()->year,
                'from_date' => now()->startOfMonth()->toDateString(),
                'to_date' => now()->endOfMonth()->toDateString(),
                'due_date' => now()->addDays(5)->toDateString(),
                'room_price' => 3000000,
                'total_amount' => 3500000,
                'paid_amount' => 0,
                'remaining_amount' => 3500000,
                'status' => 'unpaid',
            ]
        );

        $this->invoiceB = Invoice::firstOrCreate(
            ['invoice_code' => 'INV-TEST-B'],
            [
                'contract_id' => $this->contractB->id,
                'room_id' => $this->roomB->id,
                'month' => now()->month,
                'year' => now()->year,
                'from_date' => now()->startOfMonth()->toDateString(),
                'to_date' => now()->endOfMonth()->toDateString(),
                'due_date' => now()->addDays(5)->toDateString(),
                'room_price' => 3500000,
                'total_amount' => 4000000,
                'paid_amount' => 0,
                'remaining_amount' => 4000000,
                'status' => 'unpaid',
            ]
        );
    }

    public function test_manager_cannot_create_or_modify_rooms_in_other_property(): void
    {
        // Manager A tries to create a room in Property B -> 403 Forbidden
        $response = $this->actingAs($this->managerA)->post('/rooms', [
            'property_id' => $this->propertyB->id,
            'room_number' => 'HACK-999',
            'floor' => 1,
            'room_type' => 'Phòng khép kín',
            'price' => 2000000,
            'area' => 15,
            'max_tenants' => 2,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
        ]);
        $response->assertStatus(403);

        // Manager A tries to view room in Property B -> 403 Forbidden
        $viewResponse = $this->actingAs($this->managerA)->get("/rooms/{$this->roomB->id}");
        $viewResponse->assertStatus(403);

        // Manager A tries to edit room in Property B -> 403 Forbidden
        $editResponse = $this->actingAs($this->managerA)->get("/rooms/{$this->roomB->id}/edit");
        $editResponse->assertStatus(403);

        // Manager A tries to delete room in Property B -> 403 Forbidden
        $delResponse = $this->actingAs($this->managerA)->delete("/rooms/{$this->roomB->id}");
        $delResponse->assertStatus(403);
    }

    public function test_manager_cannot_manage_fees_or_assets_in_other_property(): void
    {
        // Manager A tries to add fee to Room B -> 403 Forbidden
        $feeResponse = $this->actingAs($this->managerA)->post("/rooms/{$this->roomB->id}/fees", [
            'fee_name' => 'Phí hack',
            'fee_type' => 'fixed',
            'unit_price' => 50000,
            'quantity' => 1,
        ]);
        $feeResponse->assertStatus(403);

        // Manager A tries to add asset to Room B -> 403 Forbidden
        $assetResponse = $this->actingAs($this->managerA)->post("/rooms/{$this->roomB->id}/assets", [
            'name' => 'Tủ lạnh hack',
            'quantity' => 1,
            'condition' => 'Mới 100%',
            'price' => 5000000,
        ]);
        $assetResponse->assertStatus(403);
    }

    public function test_manager_cannot_access_or_tamper_with_contracts_of_other_property(): void
    {
        // Manager A tries to view Contract B -> 403 Forbidden
        $response = $this->actingAs($this->managerA)->get("/contracts/{$this->contractB->id}");
        $response->assertStatus(403);

        // Manager A tries to print Contract B -> 403 Forbidden
        $printResponse = $this->actingAs($this->managerA)->get("/contracts/{$this->contractB->id}/print");
        $printResponse->assertStatus(403);

        // Manager A tries to checkout Contract B -> 403 Forbidden
        $checkoutResponse = $this->actingAs($this->managerA)->get("/contracts/{$this->contractB->id}/checkout");
        $checkoutResponse->assertStatus(403);

        // Manager A tries to process checkout for Contract B -> 403 Forbidden
        $processResponse = $this->actingAs($this->managerA)->post("/contracts/{$this->contractB->id}/checkout", [
            'final_electricity' => 100,
            'final_water' => 20,
            'refund_amount' => 3500000,
            'room_next_status' => 'available',
        ]);
        $processResponse->assertStatus(403);
    }

    public function test_manager_cannot_manage_invoices_of_other_property(): void
    {
        // Manager A tries to view Invoice B -> 403 Forbidden
        $response = $this->actingAs($this->managerA)->get("/invoices/{$this->invoiceB->id}");
        $response->assertStatus(403);

        // Manager A tries to print Invoice B -> 403 Forbidden
        $printResponse = $this->actingAs($this->managerA)->get("/invoices/{$this->invoiceB->id}/print");
        $printResponse->assertStatus(403);

        // Manager A tries to record payment for Invoice B -> 403 Forbidden
        $payResponse = $this->actingAs($this->managerA)->post("/invoices/{$this->invoiceB->id}/payment", [
            'payment_amount' => 1000000,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);
        $payResponse->assertStatus(403);

        // Manager A tries to bulkStore invoices for Property B -> 403 Forbidden
        $bulkResponse = $this->actingAs($this->managerA)->post("/invoices/bulk-store", [
            'property_id' => $this->propertyB->id,
            'month' => now()->month,
            'year' => now()->year,
            'due_date' => now()->addDays(5)->toDateString(),
            'rooms' => [
                $this->roomB->id => [
                    'electricity_old' => 0,
                    'electricity_new' => 10,
                ]
            ]
        ]);
        $bulkResponse->assertStatus(403);
    }

    public function test_manager_cannot_view_or_tamper_with_tenants_of_other_property(): void
    {
        // Manager A tries to view Tenant B (who only rents in Property B) -> 403 Forbidden
        $response = $this->actingAs($this->managerA)->get("/tenants/{$this->tenantB->id}");
        $response->assertStatus(403);

        // Manager A tries to edit Tenant B -> 403 Forbidden
        $editResponse = $this->actingAs($this->managerA)->get("/tenants/{$this->tenantB->id}/edit");
        $editResponse->assertStatus(403);
    }

    public function test_manager_cannot_manage_maintenance_of_other_property(): void
    {
        $maintenanceB = MaintenanceRequest::create([
            'room_id' => $this->roomB->id,
            'title' => 'Hỏng điều hòa Beta',
            'status' => 'pending',
            'reported_date' => now()->toDateString(),
        ]);

        // Manager A tries to edit maintenance of Room B -> 403 Forbidden
        $editResponse = $this->actingAs($this->managerA)->get("/maintenance/{$maintenanceB->id}/edit");
        $editResponse->assertStatus(403);

        // Manager A tries to delete maintenance of Room B -> 403 Forbidden
        $delResponse = $this->actingAs($this->managerA)->delete("/maintenance/{$maintenanceB->id}");
        $delResponse->assertStatus(403);
    }

    public function test_tenant_cannot_submit_feedback_or_inspect_invoices_of_other_tenants(): void
    {
        // Tenant A tries to submit feedback on Tenant B's invoice -> 403 Forbidden
        $response = $this->actingAs($this->tenantUserA)
            ->withSession(['tenant_id' => $this->tenantA->id])
            ->post("/khach-thue/feedback/{$this->invoiceB->id}", [
                'feedback_type' => 'electricity',
                'content' => 'Khiếu nại trái phép trên hóa đơn của người khác!',
            ]);
        $response->assertStatus(403);

        // Tenant A tries to lookup Tenant B's invoice code -> 403 Forbidden
        $lookupResponse = $this->actingAs($this->tenantUserA)->get("/tra-cuu/{$this->invoiceB->invoice_code}");
        $lookupResponse->assertStatus(403);

        // Tenant A can successfully lookup their own invoice -> 200 OK
        $ownResponse = $this->actingAs($this->tenantUserA)->get("/tra-cuu/{$this->invoiceA->invoice_code}");
        $ownResponse->assertStatus(200);
        $ownResponse->assertSee($this->invoiceA->invoice_code);
    }

    public function test_manager_cannot_reply_to_feedback_of_other_property(): void
    {
        $feedbackB = InvoiceFeedback::create([
            'invoice_id' => $this->invoiceB->id,
            'tenant_id' => $this->tenantB->id,
            'feedback_type' => 'electricity',
            'content' => 'Số điện tháng này cao quá!',
            'status' => 'pending',
        ]);

        // Manager A tries to reply to feedback belonging to Property B -> 403 Forbidden
        $response = $this->actingAs($this->managerA)->post("/admin/feedbacks/{$feedbackB->id}/reply", [
            'admin_reply' => 'Can thiệp trái phép',
            'status' => 'resolved',
        ]);
        $response->assertStatus(403);

        // Manager B can reply to feedback belonging to Property B -> 302 Redirect (success)
        $validResponse = $this->actingAs($this->managerB)->post("/admin/feedbacks/{$feedbackB->id}/reply", [
            'admin_reply' => 'Đã kiểm tra lại đồng hồ công tơ, đúng chỉ số nhé bạn.',
            'status' => 'resolved',
        ]);
        $validResponse->assertStatus(302);
        $this->assertDatabaseHas('invoice_feedbacks', [
            'id' => $feedbackB->id,
            'status' => 'resolved',
        ]);
    }

    public function test_manager_cannot_create_contract_with_tenant_of_other_property(): void
    {
        // Manager A tries to create contract for Room A but with Tenant B (from Property B)
        $response = $this->actingAs($this->managerA)->post('/contracts', [
            'room_id' => $this->roomA->id,
            'tenant_id' => $this->tenantB->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'rental_price' => 3000000,
            'deposit_amount' => 3000000,
            'deposit_status' => 'held',
        ]);
        $response->assertStatus(403);
    }

    public function test_manager_dashboard_hides_landlord_net_profit_and_state_tax(): void
    {
        // Manager A viewing dashboard does not see landlord confidential cards
        $managerResponse = $this->actingAs($this->managerA)->get('/dashboard');
        $managerResponse->assertStatus(200);
        $managerResponse->assertDontSee('LỢI NHUẬN RÒNG (THU - CHI)');
        $managerResponse->assertDontSee('CHI PHÍ TRẢ NHÀ NƯỚC');
        $managerResponse->assertSee('HÓA ĐƠN CHƯA THU (NỢ)');
        $managerResponse->assertSee('HỢP ĐỒNG SẮP HẾT HẠN');

        // Admin viewing dashboard sees landlord financial summary
        $adminResponse = $this->actingAs($this->admin)->get('/dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('LỢI NHUẬN RÒNG (THU - CHI)');
        $adminResponse->assertSee('CHI PHÍ TRẢ NHÀ NƯỚC');
    }

    public function test_phone_lookup_with_credentialed_tenant_redirects_to_password_login(): void
    {
        // Tenant A has a User account. Typing their phone number must redirect to /login
        $response = $this->get("/khach-thue?phone={$this->tenantA->phone}");
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');
    }

    public function test_quick_select_in_production_is_forbidden(): void
    {
        config(['app.env' => 'production', 'app.debug' => false]);

        $response = $this->post('/khach-thue/select', [
            'tenant_id' => $this->tenantA->id,
        ]);
        $response->assertStatus(403);
    }
}
