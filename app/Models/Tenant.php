<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'id_card_number',
        'id_card_date',
        'id_card_place',
        'dob',
        'gender',
        'hometown',
        'vehicle_plate',
        'temporary_residence_status',
        'notes',
    ];

    protected $casts = [
        'id_card_date' => 'date',
        'dob' => 'date',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function currentContract(): HasOne
    {
        return $this->hasOne(Contract::class)->where('status', 'active');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(InvoiceFeedback::class);
    }

    public function getResidenceLabelAttribute(): string
    {
        return $this->temporary_residence_status === 'registered' ? 'Đã đăng ký tạm trú' : 'Chưa đăng ký';
    }

    public function getResidenceBadgeAttribute(): string
    {
        return $this->temporary_residence_status === 'registered' ? 'success' : 'danger';
    }
}
