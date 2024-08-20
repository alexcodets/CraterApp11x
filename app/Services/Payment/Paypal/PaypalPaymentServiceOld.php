<?php

namespace Crater\Services\Payment\Paypal;

use Crater\DataObject\PaymentPaypalDO;
use Crater\Models\PaypalSetting;
use Exception;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use Throwable;

class PaypalPaymentServiceOld
{
    /**
     * @throws Exception|Throwable
     */
    public static function handleCreditCard(PaymentPaypalDO $paypalDO): array
    {
        $paypalSetting = PaypalSetting::query()
            ->where('status', 'A')
            ->first();
        if (is_null($paypalSetting)) {
            throw new Exception('There was not Paypal Setting in the system');
        }

        /* @var PaypalSetting $paypalSetting */

        $gateway = Omnipay::create('PayPal_Rest');

        $gateway->initialize([
            'clientId' => $paypalSetting->public_key,
            'secret' => $paypalSetting->private_key,
            'testMode' => $paypalSetting->enviroment == 'sandbox', // Or false when you are ready for live transactions
        ]);

        $card = new CreditCard($paypalDO->getCreditCardData());
        Log::debug('new credit card', [$paypalDO->getCreditCardData()]);

        try {
            $card->validate();
        } catch (Throwable $th) {
            Log::error('There was a error while validating the credit card for Paypal');
            Log::error($th);

            throw $th;
        }

        Log::debug('Paypal Send Amount');
        Log::debug('====================');
        Log::debug('Purchase Data: ', $paypalDO->getPurchaseData($card));
        Log::debug('=============');

        $transaction = $gateway->purchase($paypalDO->getPurchaseData($card))->send();

        if ($transaction->isSuccessful()) {
            Log::debug('Transaction Successful');
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

        Log::error('Payment Failed');
        Log::error($transaction->getMessage());
        Log::error($transaction->getData());

        $data = $transaction->getData();

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
            //return $data['message'];
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
