<?php

namespace Crater\DataObject;

use Crater\Helpers\General;
use Crater\Models\PaymentsPaypal;
use Omnipay\Common\CreditCard;

class PaymentPaypalDO extends PaymentDO
{
    private PaymentsPaypal $paypalPayment;

    public function __construct(PaymentAccountData $paymentAccount, UserData $user, PaymentAmountData $amount)
    {
        parent::__construct($paymentAccount, $user, $amount);
        $this->paypalPayment = PaymentsPaypal::create([
            'company_id' => $user->company_id,
            'creator_id' => $paymentAccount->id ?? $user->id,
            'amount' => $amount->getTotal() / 100,
            'email_address' => $paymentAccount->email ?? $user->email,
            'transaction_id' => General::generateUniqueId(),
            'currency' => 'USD',
            'card_number' => substr($this->paymentAccount->decrypted_card_number, -4),
        ]);

    }

    public function getCreditCardData(): array
    {
        $date = $this->paymentAccount->decrypted_expiration_date;

        return [
            'firstName' => $this->paymentAccount->first_name,
            'lastName' => $this->paymentAccount->last_name,
            'number' => $this->paymentAccount->decrypted_card_number,
            'expiryMonth' => $this->paymentAccount->decrypted_expiration_date->format('m'),
            'expiryYear' => $this->paymentAccount->decrypted_expiration_date->format('Y'),
            'cvv' => $this->paymentAccount->decrypted_cvv,
            'billingAddress1' => $this->paymentAccount->address_1,
            'billingCountry' => $this->paymentAccount->country->name ?? null,
            'billingCity' => $this->paymentAccount->city,
            'billingPostcode' => $this->paymentAccount->zip,
            'billingState' => $this->paymentAccount->state->name ?? null,
            'transactionId' => $this->paypalPayment->transaction_id,
            'clientIp' => $this->user->customcode,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ];

    }

    public function getPurchaseData(CreditCard $card): array
    {
        return [
            'amount' => $this->amount->amount / 100,
            'currency' => 'USD',
            'card' => $card,
        ];

    }

    public function transactionSuccess($paymentData)
    {
        $this->paypalPayment->country_code = $paymentData['payer']['funding_instruments'][0]['credit_card']['billing_address']['country_code'];
        $this->paypalPayment->payment_status = $paymentData['state'];
        $this->paypalPayment->create_time = $paymentData['create_time'];
        $this->paypalPayment->save();

        return $this->paypalPayment;
    }

    public function getTransactionData($transaction): array
    {
        $paymentData = $transaction->getData();

        return [
            'transaction_id' => $transaction->getTransactionReference(),

            'email_address' => $this->user->email,
            'amount' => $this->amount->amount / 100,
            'currency' => 'USD',

            'country_code' => $paymentData['payer']['funding_instruments'][0]['credit_card']['billing_address']['country_code'],
            'payment_status' => $paymentData['state'],

            'card_number' => substr($this->paymentAccount->decrypted_card_number, -4),
            // 'card_type' => $request['card_type'],
            'create_time' => $paymentData['create_time'],
            'creator_id' => auth()->user()->id ?? null,
            'company_id' => $this->user->company_id,

        ];

    }

    public function failureResponse($data): array
    {
        return [
            'payment_gateway' => 'PaypalPro',
            'transaction_number' => 20,
            'amount' => $this->amount->getTotal() / 100,
            'customer_id' => $this->user->id,
            'description' => $data['data']['message'],
        ];

    }
}
