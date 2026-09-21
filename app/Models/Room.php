<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_number',
        'floor',
        'price',
        'area',
        'max_tenants',
        'status',
        'electricity_meter_number',
        'initial_electricity',
        'electricity_rate',
        'water_meter_number',
        'initial_water',
        'water_calculation_type',
        'water_rate',
        'internet_type',
        'internet_rate',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'area' => 'decimal:1',
        'initial_electricity' => 'decimal:1',
        'electricity_rate' => 'decimal:0',
        'initial_water' => 'decimal:1',
        'water_rate' => 'decimal:0',
        'internet_rate' => 'decimal:0',
    ];

    public function getWaterTypeLabelAttribute(): string
    {
        return match ($this->water_calculation_type) {
            'meter' => 'Theo đồng hồ (m³)',
            'per_person' => 'Theo đầu người',
            'fixed_room' => 'Khoán theo phòng',
            default => 'Theo đồng hồ',
        };
    }

    public function getInternetTypeLabelAttribute(): string
    {
        return match ($this->internet_type) {
            'fixed' => 'Khoán theo phòng',
            'per_person' => 'Theo đầu người',
            'free' => 'Miễn phí',
            default => 'Khoán theo phòng',
        };
    }

    // Helper: Tính tiền nước phòng dựa vào cách tính & số người/khối dùng
    public function calculateWaterAmount(int $occupantsCount = 1, float $usage = 0): float
    {
        return match ($this->water_calculation_type) {
            'per_person' => $this->water_rate * max(1, $occupantsCount),
            'fixed_room' => (float) $this->water_rate,
            'meter' => $this->water_rate * max(0, $usage),
            default => $this->water_rate * max(0, $usage),
        };
    }

    // Helper: Tính tiền mạng Internet Wifi phòng
    public function calculateInternetAmount(int $occupantsCount = 1): float
    {
        return match ($this->internet_type) {
            'per_person' => $this->internet_rate * max(1, $occupantsCount),
            'fixed' => (float) $this->internet_rate,
            'free' => 0.0,
            default => (float) $this->internet_rate,
        };
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(RoomFee::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(RoomAsset::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function currentContract(): HasOne
    {
        return $this->hasOne(Contract::class)->where('status', 'active');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    // Helper: Tổng số người đang ở trong phòng (Đại diện + Người ở cùng)
    public function getTotalOccupantsCountAttribute(): int
    {
        if (!$this->currentContract) {
            return 0;
        }
        return 1 + $this->currentContract->members()->count();
    }

    // Helper: Danh sách tất cả khách đang ở trong phòng
    public function getAllOccupantsAttribute(): array
    {
        if (!$this->currentContract) {
            return [];
        }
        $occupants = [];
        if ($this->currentContract->tenant) {
            $occupants[] = [
                'name' => $this->currentContract->tenant->name,
                'role' => 'Đại diện hợp đồng',
                'phone' => $this->currentContract->tenant->phone,
                'vehicle_plate' => $this->currentContract->tenant->vehicle_plate,
                'is_main' => true,
            ];
        }
        foreach ($this->currentContract->members as $member) {
            $occupants[] = [
                'name' => $member->name,
                'role' => $member->relationship ?: 'Ở cùng / Ở ghép',
                'phone' => $member->phone,
                'vehicle_plate' => $member->vehicle_plate,
                'is_main' => false,
            ];
        }
        return $occupants;
    }

    // Helper: Lấy chỉ số điện gần nhất (từ hóa đơn mới nhất hoặc chỉ số khởi tạo)
    public function getLatestElectricityReading(): float
    {
        $lastInvoice = $this->invoices()->orderBy('year', 'desc')->orderBy('month', 'desc')->first();
        if ($lastInvoice) {
            return (float) $lastInvoice->electricity_new;
        }
        return (float) $this->initial_electricity;
    }

    // Helper: Lấy chỉ số nước gần nhất
    public function getLatestWaterReading(): float
    {
        $lastInvoice = $this->invoices()->orderBy('year', 'desc')->orderBy('month', 'desc')->first();
        if ($lastInvoice) {
            return (float) $lastInvoice->water_new;
        }
        return (float) $this->initial_water;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Phòng trống',
            'occupied' => 'Đang cho thuê',
            'maintenance' => 'Đang sửa chữa / dọn dẹp',
            default => 'Không xác định'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'occupied' => 'primary',
            'maintenance' => 'warning',
            default => 'secondary'
        };
    }

    public function getWaterTypeNameAttribute(): string
    {
        return match ($this->water_calculation_type) {
            'meter' => 'Theo đồng hồ (khối m³)',
            'per_person' => 'Theo đầu người',
            'fixed_room' => 'Khoán theo phòng',
            default => 'Theo đồng hồ'
        };
    }
}
