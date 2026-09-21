<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cấu hình tiền nước & tiền mạng mặc định cho Nhà Trọ (Property)
        Schema::table('properties', function (Blueprint $table) {
            $table->string('default_water_type')->default('meter'); // meter, per_person, fixed_room
            $table->decimal('default_water_rate', 10, 0)->default(30000);
            $table->string('default_internet_type')->default('fixed'); // fixed, per_person, free
            $table->decimal('default_internet_rate', 10, 0)->default(100000);
        });

        // 2. Cấu hình tiền mạng riêng cho từng Phòng (Room)
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('internet_type')->default('fixed'); // fixed, per_person, free
            $table->decimal('internet_rate', 10, 0)->default(100000);
        });

        // 3. Tài khoản đăng nhập liên kết Khách Thuê (User -> Tenant)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
        });

        // 4. Bổ sung cách tính nước vào hóa đơn để lưu vết lịch sử
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('water_calculation_type')->default('meter'); // meter, per_person, fixed_room
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('water_calculation_type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['internet_type', 'internet_rate']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'default_water_type',
                'default_water_rate',
                'default_internet_type',
                'default_internet_rate'
            ]);
        });
    }
};
