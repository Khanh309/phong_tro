<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImageUploadAndCleanupTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('role', 'admin')->first();
        Storage::fake('public');
    }

    public function test_can_create_property_with_image(): void
    {
        $file = UploadedFile::fake()->image('building.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post('/properties', [
            'name' => 'Tòa Nhà Test Upload Ảnh',
            'address' => 'Số 99 Phố Test',
            'city' => 'Hà Nội',
            'district' => 'Cầu Giấy',
            'total_floors' => 5,
            'default_water_type' => 'meter',
            'default_water_rate' => 30000,
            'default_internet_type' => 'fixed',
            'default_internet_rate' => 100000,
            'image' => $file,
        ]);

        $response->assertStatus(302);

        $property = Property::where('name', 'Tòa Nhà Test Upload Ảnh')->first();
        $this->assertNotNull($property);
        $this->assertNotNull($property->image);
        Storage::disk('public')->assertExists($property->image);
    }

    public function test_can_update_and_delete_property_image(): void
    {
        $oldFile = UploadedFile::fake()->image('old_building.jpg');
        $storedOldPath = $oldFile->store('properties', 'public');

        $property = Property::create([
            'name' => 'Tòa Cần Thay Ảnh',
            'address' => 'Số 10 Phố Test',
            'city' => 'Hà Nội',
            'total_floors' => 4,
            'default_water_type' => 'meter',
            'default_water_rate' => 30000,
            'default_internet_type' => 'fixed',
            'default_internet_rate' => 100000,
            'image' => $storedOldPath,
        ]);

        Storage::disk('public')->assertExists($storedOldPath);

        // Upload new image -> Old image should be deleted
        $newFile = UploadedFile::fake()->image('new_building.jpg');

        $response = $this->actingAs($this->admin)->put("/properties/{$property->id}", [
            'name' => 'Tòa Cần Thay Ảnh Đã Đổi',
            'address' => 'Số 10 Phố Test',
            'city' => 'Hà Nội',
            'total_floors' => 4,
            'default_water_type' => 'meter',
            'default_water_rate' => 30000,
            'default_internet_type' => 'fixed',
            'default_internet_rate' => 100000,
            'image' => $newFile,
        ]);

        $response->assertStatus(302);
        Storage::disk('public')->assertMissing($storedOldPath);

        $property->refresh();
        $this->assertNotNull($property->image);
        $this->assertNotEquals($storedOldPath, $property->image);
        Storage::disk('public')->assertExists($property->image);
    }

    public function test_deleting_property_cleans_up_its_image(): void
    {
        $file = UploadedFile::fake()->image('facade.jpg');
        $path = $file->store('properties', 'public');

        $property = Property::create([
            'name' => 'Tòa Nhà Chuẩn Bị Xóa',
            'address' => 'Số 88 Phố Xóa',
            'city' => 'Hà Nội',
            'total_floors' => 3,
            'default_water_type' => 'meter',
            'default_water_rate' => 30000,
            'default_internet_type' => 'fixed',
            'default_internet_rate' => 100000,
            'image' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)->delete("/properties/{$property->id}");
        $response->assertStatus(302);

        Storage::disk('public')->assertMissing($path);
        $this->assertNull(Property::find($property->id));
    }

    public function test_can_create_room_with_multiple_images(): void
    {
        $property = Property::first();
        $img1 = UploadedFile::fake()->image('bedroom.jpg');
        $img2 = UploadedFile::fake()->image('bathroom.jpg');
        $img3 = UploadedFile::fake()->image('balcony.jpg');

        $response = $this->actingAs($this->admin)->post('/rooms', [
            'property_id' => $property->id,
            'room_number' => 'TEST_P999',
            'floor' => 2,
            'price' => 3500000,
            'area' => 25.0,
            'max_tenants' => 2,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
            'internet_type' => 'fixed',
            'internet_rate' => 100000,
            'images' => [$img1, $img2, $img3],
        ]);

        $response->assertStatus(302);

        $room = Room::where('room_number', 'TEST_P999')->first();
        $this->assertNotNull($room);
        $this->assertIsArray($room->images);
        $this->assertCount(3, $room->images);

        foreach ($room->images as $imgPath) {
            Storage::disk('public')->assertExists($imgPath);
        }

        $this->assertNotEmpty($room->primary_image_url);
        $this->assertCount(3, $room->all_image_urls);
    }

    public function test_can_update_room_and_delete_specific_image(): void
    {
        $property = Property::first();
        $f1 = UploadedFile::fake()->image('pic1.jpg')->store('rooms', 'public');
        $f2 = UploadedFile::fake()->image('pic2.jpg')->store('rooms', 'public');

        $room = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST_P888',
            'floor' => 3,
            'price' => 4000000,
            'area' => 28.0,
            'max_tenants' => 2,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
            'internet_type' => 'fixed',
            'internet_rate' => 100000,
            'images' => [$f1, $f2],
        ]);

        Storage::disk('public')->assertExists($f1);
        Storage::disk('public')->assertExists($f2);

        // Update: delete $f1 and upload $f3
        $f3 = UploadedFile::fake()->image('pic3.jpg');

        $response = $this->actingAs($this->admin)->put("/rooms/{$room->id}", [
            'property_id' => $property->id,
            'room_number' => 'TEST_P888',
            'floor' => 3,
            'price' => 4000000,
            'area' => 28.0,
            'max_tenants' => 2,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
            'internet_type' => 'fixed',
            'internet_rate' => 100000,
            'delete_images' => [$f1],
            'images' => [$f3],
        ]);

        $response->assertStatus(302);

        // $f1 must be deleted from storage
        Storage::disk('public')->assertMissing($f1);
        // $f2 must remain
        Storage::disk('public')->assertExists($f2);

        $room->refresh();
        $this->assertCount(2, $room->images);
        $this->assertNotContains($f1, $room->images);
        $this->assertContains($f2, $room->images);
    }

    public function test_deleting_room_cleans_up_all_image_files_from_storage(): void
    {
        $property = Property::first();
        $f1 = UploadedFile::fake()->image('room_a.jpg')->store('rooms', 'public');
        $f2 = UploadedFile::fake()->image('room_b.jpg')->store('rooms', 'public');

        $room = Room::create([
            'property_id' => $property->id,
            'room_number' => 'TEST_P777',
            'floor' => 1,
            'price' => 3000000,
            'area' => 20.0,
            'max_tenants' => 2,
            'status' => 'available',
            'initial_electricity' => 0,
            'electricity_rate' => 3500,
            'initial_water' => 0,
            'water_calculation_type' => 'meter',
            'water_rate' => 30000,
            'internet_type' => 'fixed',
            'internet_rate' => 100000,
            'images' => [$f1, $f2],
        ]);

        Storage::disk('public')->assertExists($f1);
        Storage::disk('public')->assertExists($f2);

        $response = $this->actingAs($this->admin)->delete("/rooms/{$room->id}");
        $response->assertStatus(302);

        // Both files must be deleted from storage
        Storage::disk('public')->assertMissing($f1);
        Storage::disk('public')->assertMissing($f2);
        $this->assertNull(Room::find($room->id));
    }
}
