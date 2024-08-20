<?php

namespace Crater\Services\Payment\Paypal;

use Crater\Models\PaypalSetting;
use Exception;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use Throwable;

class PaypalPaymentService
{
    /**
     * @throws Exception|Throwable
     */
    public static function handleCreditCard(PaymentPaypalData &$paypalDO, ?PaypalSetting $settings = null): array
    {
        $paypalSetting = $settings ?? PaypalSetting::query()
            ->where('status', 'A')
            ->first();
        if (is_null($paypalSetting)) {
            throw new Exception('There was not Paypal Setting in the system');
        }
        $paypalDO->setting_id = $paypalSetting->id;

        $gateway = Omnipay::create('PayPal_Rest');

        $gateway->initialize([
            'clientId' => $paypalSetting->public_key,
            'secret' => $paypalSetting->private_key,
            'testMode' => $paypalSetting->enviroment == 'sandbox', // Or false when you are ready for live transactions
        ]);

        $card = new CreditCard($paypalDO->getCreditCardData());
        //Log::debug('new credit card', [$paypalDO->getCreditCardData()]);

        try {
            $card->validate();
        } catch (Throwable $th) {
            Log::error('There was a error while validating the credit card for Paypal');
            Log::error($th);

            throw $th;
        }

        Log::debug('Purchase Data: ', $paypalDO->getPurchaseData($card));

        $transaction = $gateway->purchase($paypalDO->getPurchaseData($card))->send();

        if ($transaction->isSuccessful()) {
            Log::debug('Paypal Rest Transaction Successful');
            $paymentData = $transaction->getData();
            $payment_paypal = $paypalDO->transactionSuccess($paymentData);

            return [
                'success' => true,
                'payment_id' => $transaction->getTransactionReference(),
                'data' => $payment_paypal,
                'payment_gateway' => 'paypal',
                'paymentData' => $paymentData,
                'message' => 'Success',
            ];
        }
        $data = $transaction->getData();

        Log::error('Payment Failed');
        Log::error('Api Paypal Response Data: ', $data);


        return [
            'success' => false,
            'payment_gateway' => 'paypal',
            'message' => self::getErrorMessage($data),
            'data' => $data,
        ];

    }

    public static function getErrorMessage(array $data): string
    {
        $response = '';
        if (array_key_exists('name', $data) && $data['name'] == 'VALIDATION_ERROR') {
            if (isset($data['details'])) {
                foreach ($data['details'] as $detail) {
                    if (isset($detail['issue'])) {
                        $response .= '- '.$detail['issue'];
                    }
                }
            }

            return $response;
        }

        return $data['message'];

    }
}
