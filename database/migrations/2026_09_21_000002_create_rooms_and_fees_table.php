<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('room_number'); // vd: 101, 201
            $table->integer('floor')->default(1);
            $table->decimal('price', 12, 0); // Tiền phòng / tháng
            $table->decimal('area', 6, 1)->default(20.0); // m2
            $table->integer('max_tenants')->default(2);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            
            // Công tơ điện riêng của phòng
            $table->string('electricity_meter_number')->nullable();
            $table->decimal('initial_electricity', 10, 1)->default(0);
            $table->decimal('electricity_rate', 10, 0)->default(3500); // VNĐ/kWh
            
            // Nước riêng hoặc tính theo người/phòng
            $table->string('water_meter_number')->nullable();
            $table->decimal('initial_water', 10, 1)->default(0);
            $table->enum('water_calculation_type', ['meter', 'per_person', 'fixed_room'])->default('meter');
            $table->decimal('water_rate', 10, 0)->default(30000); // VNĐ/khối hoặc người/phòng
            
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('room_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('fee_name'); // Tiền rác, Tiền mạng wifi, Gửi xe máy...
            $table->enum('fee_type', ['fixed', 'per_person', 'per_unit'])->default('fixed');
            $table->decimal('unit_price', 12, 0);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('room_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('name'); // Điều hòa, nóng lạnh, giường, tủ...
            $table->integer('quantity')->default(1);
            $table->string('condition')->default('Hoạt động tốt');
            $table->decimal('price', 12, 0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_assets');
        Schema::dropIfExists('room_fees');
        Schema::dropIfExists('rooms');
    }
};
