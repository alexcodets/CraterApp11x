<?php

namespace Crater\Services\Payment\Stripe;

use Crater\Models\StripeSetting;
use Crater\Services\Payment\Stripe\Exceptions\StripeConfigurationModelNotFoundException;
use Exception;
use Log;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

class StripePaymentService
{
    /**
     * @throws Exception
     */
    public static function handleCreditCard(StripeData $data): array
    {
        Log::debug('Lets try to recharge');
        $setting = StripeSetting::query()
            ->where('status', 'A')
            ->first();

        /* @var */
        if (is_null($setting)) {
            throw new StripeConfigurationModelNotFoundException();
        }

        $card = new CreditCard($data->getCreditCardData());
        Log::debug('After card creation');

        $gateway = Omnipay::create('Stripe');
        /* @var \Omnipay\Stripe\Gateway $gateway */
        /* @var StripeSetting $setting */

        $gateway->setApiKey($setting->secret_key);

        try {
            $transaction = $gateway->purchase($data->getPurchaseData($card))->send();
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());

            return [
                'success' => false,
                'payment_gateway' => 'stripe',
                'message' => $th->getMessage(),
                'data' => [],
            ];
        }

        if ($transaction->isSuccessful()) {
            Log::debug('Transaction Successful');
            $paymentData = $transaction->getData();

            return [
                'success' => true,
                'payment_id' => $transaction->getTransactionReference(),
                'payment_gateway' => 'stripe',
                'paymentData' => $paymentData,
            ];
        }

        Log::error($transaction->getMessage());

        return [
            'success' => false,
            'payment_gateway' => 'stripe',
            'message' => $transaction->getMessage(),
            'data' => $transaction->getData(),
        ];

    }
}
