<?php

namespace Crater\Services\Payment\Authorize;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\PaymentDO;
use Crater\DataObject\UserData;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use DesolatorMagno\AuthorizePhp\Api\Contract\V1\TransactionResponseType;
use Illuminate\Support\Facades\Crypt;

class PaymentAuthorizeDO extends PaymentDO
{
    public function __construct(PaymentAccountData $paymentAccount, UserData $user, PaymentAmountData $amount)
    {
        parent::__construct($paymentAccount, $user, $amount);
    }

    public function getAuthorizeData(): array
    {
        //Log::debug('Authorize data');
        $setting = CompanySetting::where('option', 'currency')->where('company_id', $this->user->company_id)->first()->value ?? null;
        $currency_code = $setting ? (Currency::find($setting)->code ?? 'USD') : 'USD';

        return [
            'name' => $this->user->name,
            'type_client' => $this->user->customer_type == 'R' ? 'individual' : 'business',
            'company_name' => $this->user->customer_type == 'B' ? $this->user->name : $this->user->first_name.' '.$this->user->last_name,
            'currency_code' => $currency_code,
            'payment_aleatory' => rand(1000, 9999),
            'transaction_id' => rand(100000000, 999999999),
            'amount_total' => $this->amount->getTotalMini(),
            'expiration_date' => $this->paymentAccount->formatted_decrypted_expiration_date,
            'card_number' => $this->paymentAccount->decrypted_card_number,
            'cvv' => $this->paymentAccount->decrypted_cvv,
            'invoice_number' => $this->paymentAccount->invoiceNumber ?? null,
            'description' => 'Charge to restore credit',
            //'po_number'        => "{$this->paymentNumber}-{$paymentAleatoryNumber}",
            'custom_code' => $this->user->customcode,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'address_street_1' => $this->paymentAccount->address_1,
            'city' => $this->paymentAccount->city,
            'state' => $this->paymentAccount->state->name ?? null,
            'zip' => $this->paymentAccount->zip,
            'country' => $this->paymentAccount->country->name ?? null,
            'phone' => $this->user->phone,
            'email' => $this->paymentAccount->email ?? $this->user->email,
        ];
    }

    public function getCreditCardData(): array
    {
        return [
            'expiration_date' => $this->paymentAccount->formatted_decrypted_expiration_date,
            'card_number' => $this->paymentAccount->decrypted_card_number,
            'cvv' => $this->paymentAccount->decrypted_cvv,
        ];
    }

    public function getBankAccountData(): array
    {
        return [
            'routing_number' => $this->paymentAccount->decrypted_routing_number,
            'account_number' => $this->paymentAccount->decrypted_account_number,
            'name_on_account' => $this->paymentAccount->getNameOnAccount(),
            'ACH_type' => $this->paymentAccount->decrypted_ach_type ? strtolower($this->paymentAccount->decrypted_ach_type) : null,
            'bank_name' => $this->paymentAccount->decrypted_bank_name,
        ];
    }

    public function successResponse(TransactionResponseType $transactionResponse): array
    {
        //$transactionResponse->getMessages()[0]->getDescription()
        return [
            'success' => true,
            'payment_gateway' => 'Authorize',
            'transaction_number' => $transactionResponse->getTransId(),
            'amount' => $this->amount->amount,
            'customer_id' => $this->user->id,
            'description' => $this->paymentAccount->getDescription(),
            'payment_paypal_id' => null,
            'invoice_id' => $this->paymentAccount->invoiceId,
        ];

    }

    public function failResponse(string $error, ?string $code = null): array
    {
        return [
            'success' => false,
            'payment_gateway' => 'Authorize',
            'transaction_number' => null,
            'amount' => $this->amount->amount,
            'customer_id' => $this->user->id,
            'description' => $this->paymentAccount->getDescription(),
            'error_description' => $error,
            'message' => $error,
            'code_error' => $code,
            'payment_paypal_id' => null,
            'invoice_id' => '',
        ];

    }

    public function saveChargeAuthorize(array $authorizeData): array
    {
        return [
            'expiration_date' => $this->paymentAccount->expiration_date,
            'transaction_id' => $authorizeData['transaction_number'],
            'payer_email' => $this->paymentAccount->email ?? $this->user->email,
            'amount' => $this->amount->amount,
            'currency' => 'USD',
            'payment_status' => 'Captured',
            'card_number' => substr($this->paymentAccount->decrypted_card_number, -4),
            'credit_card' => $this->encryptIfNeeded($this->paymentAccount->credit_card),
            'credit_card_full' => $this->encryptIfNeeded($this->paymentAccount->card_number),
            'name' => $this->encryptIfNeeded($this->paymentAccount->first_name),
            'address_street_1' => $this->encryptIfNeeded($this->paymentAccount->address_1),
            'city' => $this->encryptIfNeeded($this->paymentAccount->city),
            'state' => $this->encryptIfNeeded($this->paymentAccount->state->name ?? null),
            'zip' => $this->encryptIfNeeded($this->paymentAccount->zip),
            'country' => $this->encryptIfNeeded($this->paymentAccount->country->name ?? null),
            'country_id' => $this->paymentAccount->country->id ?? null,
            'state_id' => $this->paymentAccount->state->id ?? null,
            'address_street_2' => null,
            'phone' => $this->user->phone,
            'creator_id' => $this->paymentAccount->id,
            'company_id' => $this->user->company_id,
            'ACH_type' => $this->encryptIfNeeded($this->paymentAccount->decrypted_ach_type),
            'account_number' => $this->encryptIfNeeded($this->paymentAccount->account_number),
            'bank_name' => $this->encryptIfNeeded($this->paymentAccount->bank_name),
            'name_on_account' => $this->encryptIfNeeded($this->paymentAccount->first_name),
            'num_check' => $this->encryptIfNeeded($this->paymentAccount->num_check),
            'routing_number' => $this->encryptIfNeeded($this->paymentAccount->routing_number),
            'authorize_setting_id' => $this->setting_id,
        ];

    }

    public function encryptIfNeeded(?string $text): ?string
    {
        if ($text && strlen($text) < 45) {
            Crypt::encryptString($text);
        }

        return $text;

    }
}
