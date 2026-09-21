<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'contract_id',
        'room_id',
        'month',
        'year',
        'from_date',
        'to_date',
        'due_date',
        'electricity_old',
        'electricity_new',
        'electricity_usage',
        'electricity_rate',
        'electricity_total',
        'water_old',
        'water_new',
        'water_usage',
        'water_rate',
        'water_total',
        'water_calculation_type',
        'room_price',
        'fees_detail',
        'other_fees',
        'discount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'fees_detail' => 'array',
        'electricity_old' => 'decimal:1',
        'electricity_new' => 'decimal:1',
        'electricity_usage' => 'decimal:1',
        'electricity_rate' => 'decimal:0',
        'electricity_total' => 'decimal:0',
        'water_old' => 'decimal:1',
        'water_new' => 'decimal:1',
        'water_usage' => 'decimal:1',
        'water_rate' => 'decimal:0',
        'water_total' => 'decimal:0',
        'room_price' => 'decimal:0',
        'other_fees' => 'decimal:0',
        'discount' => 'decimal:0',
        'total_amount' => 'decimal:0',
        'paid_amount' => 'decimal:0',
        'remaining_amount' => 'decimal:0',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceFeedback::class);
    }

    public function getStatusLabelAttribute(): string
    {
        // Tự động kiểm tra quá hạn nếu chưa thanh toán và ngày hiện tại > due_date
        if (in_array($this->status, ['unpaid', 'partially_paid']) && now()->startOfDay()->gt($this->due_date)) {
            return 'Quá hạn nộp tiền';
        }

        return match ($this->status) {
            'paid' => 'Đã thanh toán',
            'partially_paid' => 'Đã trả một phần',
            'unpaid' => 'Chưa thanh toán',
            'overdue' => 'Quá hạn',
            default => 'Chưa thanh toán'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        if (in_array($this->status, ['unpaid', 'partially_paid']) && now()->startOfDay()->gt($this->due_date)) {
            return 'danger';
        }

        return match ($this->status) {
            'paid' => 'success',
            'partially_paid' => 'info',
            'unpaid' => 'warning',
            'overdue' => 'danger',
            default => 'secondary'
        };
    }

    // Helper tạo link VietQR
    public function getVietQrUrlAttribute(): string
    {
        $property = $this->room?->property;
        if (!$property || empty($property->bank_name) || empty($property->bank_account_number)) {
            return '';
        }

        // Mã ngân hàng phổ biến (MB, VCB, TCB, VPB, ICB...)
        $bankName = urlencode(strtoupper(trim($property->bank_name)));
        $accountNo = trim($property->bank_account_number);
        $accountName = urlencode(strtoupper(trim($property->bank_account_holder ?? 'CHU NHA')));
        $amount = (int) $this->remaining_amount;
        $memo = urlencode("TT TIEN NHA P{$this->room->room_number} T{$this->month}_{$this->year}");

        return "https://img.vietqr.io/image/{$bankName}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$memo}&accountName={$accountName}";
    }

    // Helper tạo nội dung tin nhắn Zalo/SMS nhắc nộp tiền
    public function generateZaloMessage(): string
    {
        $roomNo = $this->room->room_number ?? '';
        $propertyName = $this->room->property->name ?? 'Nhà trọ';
        $tenantName = $this->contract?->tenant?->name ?? 'Quý khách';
        $totalFormatted = number_format($this->remaining_amount, 0, ',', '.') . 'đ';
        $dueDateFormatted = $this->due_date ? $this->due_date->format('d/m/Y') : '';
        $bank = $this->room->property->bank_name ?? '';
        $stk = $this->room->property->bank_account_number ?? '';
        $chuTk = $this->room->property->bank_account_holder ?? '';

        $msg = "Xin chào {$tenantName} (Phòng {$roomNo} - {$propertyName}),\n";
        $msg .= "Ban quản lý gửi thông báo tiền phòng Tháng {$this->month}/{$this->year}:\n";
        $msg .= "- Tiền phòng: " . number_format($this->room_price, 0, ',', '.') . "đ\n";
        $msg .= "- Tiền điện ({$this->electricity_old} -> {$this->electricity_new} = {$this->electricity_usage} kWh): " . number_format($this->electricity_total, 0, ',', '.') . "đ\n";
        $msg .= "- Tiền nước ({$this->water_usage} khối/người): " . number_format($this->water_total, 0, ',', '.') . "đ\n";
        
        if (!empty($this->fees_detail) && is_array($this->fees_detail)) {
            foreach ($this->fees_detail as $fee) {
                $msg .= "- {$fee['name']}: " . number_format($fee['amount'], 0, ',', '.') . "đ\n";
            }
        }
        
        $msg .= "-----------------------------\n";
        $msg .= "👉 TỔNG TIỀN CẦN THANH TOÁN: {$totalFormatted}\n";
        $msg .= "⏰ Hạn nộp: Trước ngày {$dueDateFormatted}\n\n";
        $msg .= "Thông tin chuyển khoản:\n";
        $msg .= "STK: {$stk}\n";
        $msg .= "Ngân hàng: {$bank}\n";
        $msg .= "Chủ TK: {$chuTk}\n";
        $msg .= "Nội dung: P{$roomNo} T{$this->month} {$this->year}\n";
        $msg .= "Xin cảm ơn!";

        return $msg;
    }
}
