<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique(); // HD-YYYYMM-ROOM
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('due_date'); // Hạn nộp tiền
            
            // Điện
            $table->decimal('electricity_old', 10, 1)->default(0);
            $table->decimal('electricity_new', 10, 1)->default(0);
            $table->decimal('electricity_usage', 10, 1)->default(0);
            $table->decimal('electricity_rate', 10, 0)->default(0);
            $table->decimal('electricity_total', 12, 0)->default(0);
            
            // Nước
            $table->decimal('water_old', 10, 1)->default(0);
            $table->decimal('water_new', 10, 1)->default(0);
            $table->decimal('water_usage', 10, 1)->default(0);
            $table->decimal('water_rate', 10, 0)->default(0);
            $table->decimal('water_total', 12, 0)->default(0);
            
            // Phòng & Dịch vụ
            $table->decimal('room_price', 12, 0)->default(0);
            $table->json('fees_detail')->nullable(); // Chi tiết các phí dịch vụ phòng
            $table->decimal('other_fees', 12, 0)->default(0); // Tổng phí dịch vụ
            $table->decimal('discount', 12, 0)->default(0);
            
            // Tổng thanh toán
            $table->decimal('total_amount', 12, 0)->default(0);
            $table->decimal('paid_amount', 12, 0)->default(0);
            $table->decimal('remaining_amount', 12, 0)->default(0);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'overdue'])->default('unpaid');
            $table->enum('payment_method', ['transfer', 'cash'])->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Chi phí Nhà trọ trả cho Nhà nước & Chi phí Vận hành (DÒNG CHI RIÊNG BIỆT)
        Schema::create('property_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->string('expense_type'); // electricity_evn, water_supply, state_tax, internet_bill, waste_collection, maintenance_repair, other
            $table->string('title'); // Tiêu đề chi phí
            $table->decimal('amount', 12, 0); // Số tiền chi ra
            $table->decimal('total_meter_usage', 10, 1)->nullable(); // Chỉ số tiêu thụ tổng (kWh điện EVN hoặc m3 nước tổng)
            $table->date('payment_date');
            $table->string('paid_by')->nullable(); // Người chi
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Báo hỏng & Yêu cầu sửa chữa
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 0)->default(0);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->date('reported_date');
            $table->date('resolved_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('property_expenses');
        Schema::dropIfExists('invoices');
    }
};
