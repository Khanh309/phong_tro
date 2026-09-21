<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_code',
        'room_id',
        'tenant_id',
        'start_date',
        'end_date',
        'rental_price',
        'deposit_amount',
        'deposit_status',
        'status',
        'terms',
        'terminated_at',
        'checkout_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'terminated_at' => 'date',
        'rental_price' => 'decimal:0',
        'deposit_amount' => 'decimal:0',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ContractMember::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Đang hiệu lực',
            'expiring_soon' => 'Sắp hết hạn',
            'terminated' => 'Đã thanh lý / Kết thúc',
            default => 'Không xác định'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'expiring_soon' => 'warning',
            'terminated' => 'secondary',
            default => 'secondary'
        };
    }

    public function getDepositStatusLabelAttribute(): string
    {
        return match ($this->deposit_status) {
            'held' => 'Đang giữ cọc',
            'refunded' => 'Đã hoàn cọc',
            'deducted' => 'Đã cấn trừ',
            default => 'Không rõ'
        };
    }
}
