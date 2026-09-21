<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RentalBusinessLogicTest extends TestCase
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

    public function test_can_create_property(): void
    {
        $response = $this->post('/properties', [
            'name' => 'Nhà Trọ Test Logic',
            'address' => '123 Đường Test',
            'city' => 'Hà Nội',
            'district' => 'Đống Đa',
            'total_floors' => 3,
            'bank_name' => 'MBBANK',
            'bank_account_number' => '1122334455',
            'bank_account_holder' => 'TEST USER',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('properties', ['name' => 'Nhà Trọ Test Logic']);
    }

    public function test_can_create_room_and_rent_it(): void
    {
        $property = Property::first();

        // 1. Tạo phòng mới
        $response = $this->post('/rooms', [
            'property_id' => $property->id,
            'room_number' => '999',
            'floor' => 9,
            'price' => 3000000,
            'area' => 20.0,
            'max_tenants' => 2,
            'status' => 'available',
            'electricity_meter_number' => 'EM-TEST-999',
            'initial_electricity' => 100,
            'electricity_rate' => 3500,
            'water_meter_number' => 'WM-TEST-999',
            'initial_water' => 10,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
        ]);

        $response->assertRedirect();
        $room = Room::where('room_number', '999')->first();
        $this->assertNotNull($room);
        $this->assertEquals('available', $room->status);

        // 2. Tạo khách thuê
        $tenant = Tenant::create([
            'name' => 'Khách Test 999',
            'phone' => '0988999888',
            'gender' => 'Nam',
            'temporary_residence_status' => 'registered',
        ]);

        // 3. Ký hợp đồng thuê
        $response = $this->post('/contracts', [
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'rental_price' => 3000000,
            'deposit_amount' => 3000000,
            'deposit_status' => 'held',
            'terms' => 'Nội quy test',
        ]);

        $response->assertRedirect();
        // Phòng phải tự động đổi trạng thái sang occupied
        $room->refresh();
        $this->assertEquals('occupied', $room->status);

        // 4. Lập hóa đơn và thanh toán
        $response = $this->post('/invoices', [
            'room_id' => $room->id,
            'month' => 10,
            'year' => 2026,
            'due_date' => now()->addDays(5)->toDateString(),
            'electricity_old' => 100,
            'electricity_new' => 150, // 50 kWh * 3500 = 175.000đ
            'electricity_rate' => 3500,
            'water_old' => 10,
            'water_new' => 14,        // 4 m3 * 30000 = 120.000đ
            'water_rate' => 30000,
            'room_price' => 3000000,
            'discount' => 0,
        ]);

        $response->assertRedirect();
        $invoice = Invoice::where('room_id', $room->id)->where('month', 10)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(50, $invoice->electricity_usage);
        $this->assertEquals(175000, $invoice->electricity_total);
        $this->assertEquals(120000, $invoice->water_total);

        // 5. Ghi nhận thanh toán hóa đơn
        $this->post("/invoices/{$invoice->id}/payment", [
            'payment_amount' => $invoice->total_amount,
            'payment_method' => 'transfer',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->remaining_amount);
    }
}
