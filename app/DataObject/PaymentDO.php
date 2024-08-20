<?php

namespace Crater\DataObject;

use Crater\Helpers\General;
use Crater\Models\Payment;
use Crater\Traits\PaymentTrait;
use Illuminate\Support\Facades\Cache;
use Log;

class PaymentDO
{
    use PaymentTrait;

    protected PaymentAccountData $paymentAccount;

    protected UserData $user;

    protected string $paymentNumber;

    public PaymentAmountData $amount;

    public ?int $setting_id;

    public function __construct(PaymentAccountData $paymentAccount, UserData $user, PaymentAmountData $amount, ?int $id = null)
    {
        $this->paymentAccount = $paymentAccount;
        $this->user = $user;
        $this->amount = $amount;
        $this->setting_id = $id;

    }

    public function getMailData($extra): array
    {
        return [
            'date' => now()->format('Y-m-d'),
            'mode' => $this->paymentAccount->payment_account_type,
            'amount' => $this->amount->amount / 100,
            'card_number' => substr($this->paymentAccount->decrypted_card_number, -4),
            'cvv' => $this->paymentAccount->credit_card,
            'expirationDate' => $this->paymentAccount->decrypted_expiration_date,
            'extra_data' => $extra,
        ];
    }

    public function getDataPaymentData(array $response): array
    {
        return array_filter([
            'payment_date' => now()->format('Y-m-d'),
            'amount' => $this->amount->amount,
            'user_id' => $this->user->id,
            'credit_card' => $this->paymentAccount->credit_card,
            'company_id' => $this->user->company_id,
            'payment_number' => $this->freshPaymentNumber(),
            'payment_method_id' => $response['payment_method_id'],
            'authorize_id' => $response['authorize_id'] ?? null,
            'aux_vault_id' => $response['aux_vault_id'] ?? null,
            'payment_paypal_id' => $response['payment_paypal_id'] ?? null,
            'payment_prepaid' => 0,
            'notes' => $response['note'] ?? 'Charge to restore credit',
            'invoice_id' => $response['invoice_id'] ?? null,
            'unique_hash' => General::generateUniqueId(),
        ], function ($value) {
            return ! is_null($value) && ! $value == '';
        });

    }

    public function generateSuccessPayment($response): Payment
    {
        Log::debug('GSP:Before Lock '.now()->toTimeString());
        $payment = Cache::lock('payment_generate', 10)->block(12, function () use ($response) {
            Log::debug('GSP:Inside Lock '.now()->toTimeString());

            return Payment::create($this->getDataPaymentData($response));
        });
        Log::debug('GSP:Outside Lock '.now()->toTimeString());

        Log::debug("Payment {$payment->id} created from generateSuccessPayment: with payment_number: {$payment->payment_number}");

        /* @var Payment $payment */

        //$payment = $this->checkUniquenessInPayment($payment);
        //$payment->refresh();
        return $payment;

    }

    public function getAddBalanceCustomer($paymentId): array
    {
        return [
            'status' => 'A',
            'present_balance' => $this->user->balance,
            'amount_op' => $this->amount->getAmountMini(),
            'amount_final' => $this->user->balance + ($this->amount->getAmountMini()),
            'payment_id' => $paymentId,
            'user_id' => $this->user->id,
        ];
    }
}
