<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleBasedAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_access_home_page_and_browse_vacant_rooms(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tìm Thuê Phòng Trọ');
        $response->assertSee('Đăng Nhập');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Đăng Nhập Hệ Thống');
        $response->assertSee('admin@nhatro.vn');
        $response->assertSee('quanly.caugiay@nhatro.vn');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@nhatro.vn',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@nhatro.vn',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_access_all_management_pages_including_financial_reports(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/reports/financial');
        $response->assertStatus(200);
        $response->assertSee('Báo Cáo Tài Chính');

        $propResponse = $this->actingAs($admin)->get('/properties');
        $propResponse->assertStatus(200);
    }

    public function test_manager_cannot_access_admin_financial_reports(): void
    {
        $manager = User::where('role', 'manager')->first();
        $this->assertNotNull($manager);

        // Manager accessing /reports/financial gets 403 Forbidden
        $response = $this->actingAs($manager)->get('/reports/financial');
        $response->assertStatus(403);
    }

    public function test_manager_dashboard_is_scoped_to_assigned_property(): void
    {
        $manager = User::where('role', 'manager')->whereNotNull('property_id')->first();
        $this->assertNotNull($manager);

        $assignedProperty = Property::find($manager->property_id);
        $otherProperty = Property::where('id', '!=', $manager->property_id)->first();

        $response = $this->actingAs($manager)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($assignedProperty->name);

        // Manager has a fixed badge and cannot see the property switcher dropdown
        $response->assertDontSee('<select name="property_id"', false);
        if ($otherProperty) {
            $response->assertDontSee($otherProperty->name);
        }
    }

    public function test_manager_can_access_properties_index_scoped_to_their_property(): void
    {
        $manager = User::where('role', 'manager')->whereNotNull('property_id')->first();
        $this->assertNotNull($manager);

        $assignedProperty = Property::find($manager->property_id);
        $otherProperty = Property::where('id', '!=', $manager->property_id)->first();

        $response = $this->actingAs($manager)->get('/properties');
        $response->assertStatus(200);
        $response->assertSee($assignedProperty->name);
        if ($otherProperty) {
            $response->assertDontSee($otherProperty->name);
        }
    }

    public function test_manager_cannot_view_unassigned_property_details(): void
    {
        $manager = User::where('role', 'manager')->whereNotNull('property_id')->first();
        $this->assertNotNull($manager);

        $assignedProperty = Property::find($manager->property_id);
        $otherProperty = Property::where('id', '!=', $manager->property_id)->first();

        // Accessing assigned property is OK
        $okResponse = $this->actingAs($manager)->get('/properties/' . $assignedProperty->id);
        $okResponse->assertStatus(200);
        $okResponse->assertSee($assignedProperty->name);

        // Accessing unassigned property gives 403 Forbidden
        if ($otherProperty) {
            $forbiddenResponse = $this->actingAs($manager)->get('/properties/' . $otherProperty->id);
            $forbiddenResponse->assertStatus(403);
        }
    }

    public function test_manager_cannot_create_or_edit_properties(): void
    {
        $manager = User::where('role', 'manager')->whereNotNull('property_id')->first();
        $this->assertNotNull($manager);

        $createResponse = $this->actingAs($manager)->get('/properties/create');
        $createResponse->assertStatus(403);

        $editResponse = $this->actingAs($manager)->get('/properties/' . $manager->property_id . '/edit');
        $editResponse->assertStatus(403);
    }

    public function test_user_can_logout_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
