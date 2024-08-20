<?php

namespace Crater\Services\Payment\Paypal;

use Crater\Models\PaypalSetting;
use Crater\Services\Payment\Exceptions\ActiveSettingException;
use Exception;

class PaypalService
{
    private PaypalSetting $setting;

    /** @throws Exception */
    public function __construct(?PaypalSetting $setting = null)
    {

        $setting = $setting ?? PaypalSetting::where('status', 'A')->first();
        if (is_null($setting)) {
            throw new ActiveSettingException('Active paypal setting is required');
        }

        $this->setting = $setting;
    }

    public function paymentCreditCard(PaymentPaypalData $paypalDO): array
    {
        try {
            if ($paypalDO->amount == 0) {
                return [
                    'success' => false,
                    'payment_gateway' => 'paypal',
                    'message' => '- Amount cannot be zero',
                    'data' => [],
                ];
            }

            return PaypalPaymentService::handleCreditCard($paypalDO, $this->setting);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());

            return [
                'success' => false,
                'payment_gateway' => 'paypal',
                'message' => $th->getMessage(),
                'data' => [],
            ];
        }
    }

    public function validateCC(PaymentPaypalData $paypalDO): array
    {
        $paypalDO->amount = 100;

        try {
            return PaypalPaymentService::handleCreditCard($paypalDO, $this->setting);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());

            return [
                'success' => false,
                'payment_gateway' => 'paypal',
                'message' => $th->getMessage(),
                'data' => [],
            ];
        }

    }
}
