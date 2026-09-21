<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'district',
        'total_floors',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'electricity_meter_code',
        'water_meter_code',
        'default_water_type',
        'default_water_rate',
        'default_internet_type',
        'default_internet_rate',
        'description',
    ];

    protected $casts = [
        'default_water_rate' => 'decimal:0',
        'default_internet_rate' => 'decimal:0',
    ];

    public function getDefaultWaterTypeLabelAttribute(): string
    {
        return match ($this->default_water_type) {
            'meter' => 'Theo đồng hồ (m³)',
            'per_person' => 'Theo đầu người',
            'fixed_room' => 'Khoán theo phòng',
            default => 'Theo đồng hồ',
        };
    }

    public function getDefaultInternetTypeLabelAttribute(): string
    {
        return match ($this->default_internet_type) {
            'fixed' => 'Khoán theo phòng',
            'per_person' => 'Theo đầu người',
            'free' => 'Miễn phí',
            default => 'Khoán theo phòng',
        };
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PropertyExpense::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Tiện ích thống kê
    public function totalRoomsCount(): int
    {
        return $this->rooms()->count();
    }

    public function occupiedRoomsCount(): int
    {
        return $this->rooms()->where('status', 'occupied')->count();
    }

    public function availableRoomsCount(): int
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    public function occupancyRate(): float
    {
        $total = $this->totalRoomsCount();
        if ($total === 0) return 0;
        return round(($this->occupiedRoomsCount() / $total) * 100, 1);
    }
}
