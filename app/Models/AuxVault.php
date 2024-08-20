<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuxVault extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'base_amount',
        'amount',
        'card_number',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'country_code',
        'phone_number',
        'expiry_date',
        'cvv',
        'ach_routing_number',
        'ach_account_number',
        'transaction_type',
        'user_id',
        'company_id',
        'enable_identification_verification',
        'enable_fee_charges'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
