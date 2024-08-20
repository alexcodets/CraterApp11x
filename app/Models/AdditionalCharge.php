<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCharge extends Model
{
    use HasFactory;

    protected $table = 'pbx_additional_charges';

    protected $fillable = [
        'company_id',
        'creator_id',
        'profile_extension_id',
        'profile_did_id',
        'description',
        'amount',
        'total',
        'quantity',
        'status',
        'pbx_service_id'
    ];

    public static function createAdditionalCharge($additionalCharges, $quantity, $pbxServiceId)
    {
        foreach($additionalCharges as $item) {
            if($item['status'] == 1) {

                additionalCharge::create([
                    'company_id' => $item['company_id'],
                    'creator_id' => $item['creator_id'],
                    'profile_extension_id' => $item['profile_extension_id'],
                    'profile_did_id' => $item['profile_did_id'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'total' => ($quantity * $item['amount']),
                    'quantity' => $quantity,
                    'status' => $item['status'],
                    'pbx_service_id' => $pbxServiceId
                ]);
            }
        }
    }
}
