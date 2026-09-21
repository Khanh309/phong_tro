<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'month',
        'year',
        'expense_type',
        'title',
        'amount',
        'total_meter_usage',
        'payment_date',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:0',
        'total_meter_usage' => 'decimal:1',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getExpenseTypeNameAttribute(): string
    {
        return match ($this->expense_type) {
            'electricity_evn' => 'Hóa đơn Điện tổng EVN (Nhà nước)',
            'water_supply' => 'Hóa đơn Nước tổng Cấp nước',
            'state_tax' => 'Thuế môn bài / Thuế kinh doanh nộp Nhà nước',
            'internet_bill' => 'Cáp quang Internet tổng tòa nhà',
            'waste_collection' => 'Phí rác thải môi trường đô thị',
            'maintenance_repair' => 'Bảo trì / Sửa chữa chung',
            'other' => 'Chi phí vận hành khác',
            default => 'Chi phí khác'
        };
    }

    public function getExpenseTypeBadgeAttribute(): string
    {
        return match ($this->expense_type) {
            'electricity_evn' => 'warning',
            'water_supply' => 'info',
            'state_tax' => 'danger',
            'internet_bill' => 'primary',
            'waste_collection' => 'secondary',
            'maintenance_repair' => 'dark',
            default => 'secondary'
        };
    }
}
