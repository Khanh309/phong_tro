<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\ContractMember;
use App\Models\Invoice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantAccountAndFeeTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->first();
        $this->manager = User::where('role', 'manager')->first();
    }

    public function test_admin_can_create_login_account_for_tenant(): void
    {
        $tenant = Tenant::create([
            'name' => 'Nguyễn Khách Thuê Mới',
            'phone' => '0911222333',
            'email' => 'khachmoi@test.vn',
            'gender' => 'Nam',
            'temporary_residence_status' => 'registered',
        ]);

        $response = $this->actingAs($this->admin)->post(route('tenants.account.create', $tenant->id), [
            'email' => 'khachmoi@test.vn',
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'khachmoi@test.vn',
            'role' => 'tenant',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_manager_cannot_create_login_account_for_tenant(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khách Thuê Manager Thử Nghiệm',
            'phone' => '0933444555',
            'email' => 'khach.manager@test.vn',
            'gender' => 'Nữ',
            'temporary_residence_status' => 'registered',
        ]);

        $response = $this->actingAs($this->manager)->post(route('tenants.account.create', $tenant->id), [
            'email' => 'khach.manager@test.vn',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'khach.manager@test.vn',
        ]);
    }

    public function test_admin_can_reset_password_for_tenant(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khách Đổi Mật Khẩu',
            'phone' => '0944555666',
            'email' => 'doimk@test.vn',
            'gender' => 'Nam',
            'temporary_residence_status' => 'registered',
        ]);

        $user = User::create([
            'name' => $tenant->name,
            'email' => $tenant->email,
            'password' => Hash::make('oldpassword'),
            'role' => 'tenant',
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('tenants.account.reset_password', $tenant->id), [
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_admin_can_delete_tenant_account(): void
    {
        $tenant = Tenant::create([
            'name' => 'Khách Hủy Tài Khoản',
            'phone' => '0966777888',
            'email' => 'huytk@test.vn',
            'gender' => 'Nam',
            'temporary_residence_status' => 'registered',
        ]);

        $user = User::create([
            'name' => $tenant->name,
            'email' => $tenant->email,
            'password' => Hash::make('123456'),
            'role' => 'tenant',
            'tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('tenants.account.destroy', $tenant->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_tenant_can_login_and_is_redirected_to_portal(): void
    {
        $tenantUser = User::where('role', 'tenant')->first();
        $this->assertNotNull($tenantUser);

        $response = $this->post('/login', [
            'email' => $tenantUser->email,
            'password' => '123456',
        ]);

        $response->assertRedirect(route('portal.index'));
        $this->assertAuthenticatedAs($tenantUser);
        $this->assertEquals($tenantUser->tenant_id, session('tenant_id'));
    }

    public function test_tenant_cannot_access_backoffice_dashboard_and_is_redirected_to_portal(): void
    {
        $tenantUser = User::where('role', 'tenant')->first();
        $this->assertNotNull($tenantUser);

        $response = $this->actingAs($tenantUser)->get('/dashboard');
        $response->assertRedirect(route('portal.index'));

        $roomsResponse = $this->actingAs($tenantUser)->get('/rooms');
        $roomsResponse->assertRedirect(route('portal.index'));
    }

    public function test_water_and_internet_calculation_modes_on_room_model(): void
    {
        $property = Property::first();

        // 1. Phòng tính nước theo đồng hồ (meter) và wifi cố định theo phòng (fixed)
        $roomMeter = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST-METER',
            'floor' => 1,
            'price' => 3000000,
            'area' => 20,
            'max_tenants' => 3,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 10,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
            'internet_type' => 'fixed',
            'internet_rate' => 100000,
        ]);

        // 5 khối nước -> 5 * 30000 = 150000
        $this->assertEquals(150000, $roomMeter->calculateWaterAmount(1, 5));
        // Wifi cố định 100000 bất kể số người ở
        $this->assertEquals(100000, $roomMeter->calculateInternetAmount(2));

        // 2. Phòng tính nước theo đầu người (per_person) và wifi theo đầu người (per_person)
        $roomPerPerson = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST-PER-PERSON',
            'floor' => 1,
            'price' => 3000000,
            'area' => 20,
            'max_tenants' => 3,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'per_person',
            'water_rate' => 100000,
            'internet_type' => 'per_person',
            'internet_rate' => 50000,
        ]);

        // 3 người ở -> 3 * 100000 = 300000 nước
        $this->assertEquals(300000, $roomPerPerson->calculateWaterAmount(3, 0));
        // 3 người ở -> 3 * 50000 = 150000 wifi
        $this->assertEquals(150000, $roomPerPerson->calculateInternetAmount(3));

        // 3. Phòng tính nước cố định theo phòng (fixed_room) và wifi miễn phí (free)
        $roomFixed = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST-FIXED',
            'floor' => 1,
            'price' => 3000000,
            'area' => 20,
            'max_tenants' => 3,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'fixed_room',
            'water_rate' => 120000,
            'internet_type' => 'free',
            'internet_rate' => 0,
        ]);

        // Bất kể số khối hay người ở, nước cố định 120000
        $this->assertEquals(120000, $roomFixed->calculateWaterAmount(4, 99));
        // Wifi miễn phí = 0
        $this->assertEquals(0, $roomFixed->calculateInternetAmount(4));
    }

    public function test_invoice_creation_calculates_water_per_person_correctly(): void
    {
        $property = Property::first();

        $room = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST-INV-PP',
            'floor' => 2,
            'price' => 3500000,
            'area' => 25,
            'max_tenants' => 3,
            'status' => 'occupied',
            'initial_electricity' => 100,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'per_person',
            'water_rate' => 100000,
            'internet_type' => 'per_person',
            'internet_rate' => 60000,
        ]);

        $tenant = Tenant::create([
            'name' => 'Chủ HĐ Test PP',
            'phone' => '0922333444',
            'gender' => 'Nam',
            'temporary_residence_status' => 'registered',
        ]);

        $contract = Contract::create([
            'contract_code' => 'HD-TEST-PP-01',
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(5),
            'rental_price' => 3500000,
            'deposit_amount' => 3500000,
            'status' => 'active',
        ]);

        // Thêm 2 thành viên ở chung -> Tổng 3 người (1 chủ hợp đồng + 2 thành viên)
        ContractMember::create(['contract_id' => $contract->id, 'name' => 'Bạn ở ghép 1', 'phone' => '0922333445']);
        ContractMember::create(['contract_id' => $contract->id, 'name' => 'Bạn ở ghép 2', 'phone' => '0922333446']);

        // Gọi bulkStore lập hóa đơn
        $month = now()->month;
        $year = now()->year;

        $response = $this->actingAs($this->admin)->post(route('invoices.bulk_store'), [
            'property_id' => $property->id,
            'month' => $month,
            'year' => $year,
            'due_date' => now()->addDays(5)->toDateString(),
            'rooms' => [
                $room->id => [
                    'contract_id' => $contract->id,
                    'electricity_old' => 100,
                    'electricity_new' => 150, // 50 kWh * 3500 = 175,000
                    'water_old' => 0,
                    'water_new' => 0,
                ],
            ],
        ]);

        $response->assertRedirect();

        $invoice = Invoice::where('room_id', $room->id)->where('month', $month)->where('year', $year)->first();
        $this->assertNotNull($invoice);

        // 3 người ở * 100,000 = 300,000đ tiền nước
        $this->assertEquals('per_person', $invoice->water_calculation_type);
        $this->assertEquals(300000, $invoice->water_total);

        // Kiểm tra tiền điện: 50 * 3500 = 175,000
        $this->assertEquals(175000, $invoice->electricity_total);

        // Tiền phòng: 3,500,000
        $this->assertEquals(3500000, $invoice->room_price);
    }
}
