<?php

namespace Crater\Models;

use Crater\Authorize\Models\AuthorizeSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateways extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status', 'slug', 'url_img', 'company_id', 'creator_id', 'default'];

    protected $appends = [
        'isidentificationverification',
        'registrationdatafees',
        'IsPaymentFeeActive'
    ];

    public const NAME_AUTHORIZE = 'Authorize';
    public const NAME_PAYPAL = 'Paypal';
    public const NAME_AUX_VAULT = 'AuxVault';
    public const NAME_STRIPE = 'Stripe';

    public function getIsIdentificationVerificationAttribute()
    {
        switch ($this->name) {
            case 'Authorize':
                $setting = AuthorizeSetting::where('is_default', 1)->first();

                return $setting && $setting->enable_identification_verification == 1 ? 'YES' : 'NO';

            case 'Paypal':
                $setting = PaypalSetting::where('status', 'A')->first();

                return $setting && $setting->enable_identification_verification == 1 ? 'YES' : 'NO';

            case 'AuxVault':
                $setting = AuxVaultSetting::where('default', 1)->first();

                return $setting && $setting->enable_identification_verification == 1 ? 'YES' : 'NO';

            default:
                return 'NO';
        }
    }

    public function getIsPaymentFeeActiveAttribute()
    {
        switch ($this->name) {
            case 'Authorize':
                $setting = AuthorizeSetting::where('is_default', 1)->first();

                return $setting && $setting->enable_fee_charges == 1 ? 'YES' : 'NO';

            case 'Paypal':
                $setting = PaypalSetting::where('status', 'A')->first();

                return $setting && $setting->enable_fee_charges == 1 ? 'YES' : 'NO';

            case 'AuxVault':
                $setting = AuxVaultSetting::where('default', 1)->first();

                return $setting && $setting->enable_fee_charges == 1 ? 'YES' : 'NO';

            default:
                return 'NO';
        }
    }

    public function getRegistrationDataFeesAttribute()
    {
        $setting = null;

        switch ($this->name) {
            case 'Authorize':
                $setting = AuthorizeSetting::where('is_default', 1)->first();
                if ($setting) {
                    return PaymentGatewaysFee::where('payment_gateway', 'Authorize')
                        ->where('authorize_setting_id', $setting->id)
                        ->whereNULL("deleted_at")
                        ->get();
                }

                break;

            case 'Paypal':
                $setting = PaypalSetting::where('status', 'A')->first();
                if ($setting) {
                    return PaymentGatewaysFee::where('payment_gateway', 'Paypal')
                        ->where('paypal_settings_id', $setting->id)
                        ->whereNULL("deleted_at")
                        ->get();
                }

                break;

            case 'AuxVault':
                $setting = AuxVaultSetting::where('default', 1)->first();
                if ($setting) {
                    return PaymentGatewaysFee::where('payment_gateway', 'AuxVault')
                        ->where('aux_vault_setting_id', $setting->id)
                        ->whereNULL("deleted_at")
                        ->get();
                }

                break;
        }

        return null;
    }
}
