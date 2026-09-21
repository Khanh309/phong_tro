<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'quantity',
        'condition',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:0',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
