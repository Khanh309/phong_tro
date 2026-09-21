<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'fee_name',
        'fee_type',
        'unit_price',
        'quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:0',
        'quantity' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function calculateTotal(int $tenantsCount = 1): float
    {
        if ($this->fee_type === 'per_person') {
            return $this->unit_price * max(1, $tenantsCount);
        }
        return $this->unit_price * max(1, $this->quantity);
    }
}
