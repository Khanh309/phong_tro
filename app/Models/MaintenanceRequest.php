<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'title',
        'description',
        'cost',
        'status',
        'reported_date',
        'resolved_date',
    ];

    protected $casts = [
        'cost' => 'decimal:0',
        'reported_date' => 'date',
        'resolved_date' => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ xử lý',
            'in_progress' => 'Đang sửa chữa',
            'completed' => 'Đã hoàn thành',
            default => 'Chờ xử lý'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'danger',
            'in_progress' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }
}
