<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('id_card_number')->nullable(); // CCCD
            $table->date('id_card_date')->nullable();
            $table->string('id_card_place')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->default('Nam');
            $table->string('hometown')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->enum('temporary_residence_status', ['not_registered', 'registered'])->default('not_registered');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code')->unique();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rental_price', 12, 0);
            $table->decimal('deposit_amount', 12, 0)->default(0);
            $table->enum('deposit_status', ['held', 'refunded', 'deducted'])->default('held');
            $table->enum('status', ['active', 'expiring_soon', 'terminated'])->default('active');
            $table->text('terms')->nullable();
            $table->date('terminated_at')->nullable();
            $table->text('checkout_note')->nullable();
            $table->timestamps();
        });

        Schema::create('contract_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('relationship')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_members');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('tenants');
    }
};
