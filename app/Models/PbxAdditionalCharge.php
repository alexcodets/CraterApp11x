<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxAdditionalCharge extends Model
{
    use HasFactory;

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
        'pbx_service_id',
        'additional_charge_id',
    ];

    public static function createAdditionalCharge($additionalCharges, $quantity, $pbxServiceId)
    {
        foreach ($additionalCharges as $item) {
            if ($item['status'] == 1) {

                PbxAdditionalCharge::create([
                    'company_id' => $item['company_id'],
                    'creator_id' => $item['creator_id'],
                    'profile_extension_id' => $item['profile_extension_id'],
                    'profile_did_id' => $item['profile_did_id'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'total' => ($quantity * $item['amount']),
                    'quantity' => $quantity,
                    'status' => $item['status'],
                    'pbx_service_id' => $pbxServiceId,
                    'additional_charge_id' => $item['id'],
                ]);
            }
        }
    }

    public static function deleteAdditionalCharges($pbxServiceId)
    {
        PbxAdditionalCharge::where('pbx_service_id', $pbxServiceId)
            ->forceDelete();
    }

    public static function updateAdditionalCharge($quantitydid, $quantityext, $pbxServiceId)
    {

        $charges = PbxAdditionalCharge::where('pbx_service_id', $pbxServiceId)->whereNULL('deleted_at')->get();

        foreach($charges as $ch) {
            if($ch->profile_extension_id != null && $ch->profile_did_id == null) {
                $ch->quantity = $quantityext;
                $ch->total = $ch->quantity * $ch->amount;
                $ch->save();

            } else {

                if($ch->profile_extension_id == null && $ch->profile_did_id != null) {
                    $ch->quantity = $quantitydid;
                    $ch->total = $ch->quantity * $ch->amount;
                    $ch->save();

                }

            }

        }
    }
}
