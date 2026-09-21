<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'name',
        'phone',
        'id_card_number',
        'relationship',
        'vehicle_plate',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
