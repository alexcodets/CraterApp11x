<?php

namespace Crater\Services\Payment\AuxVault;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\PaymentDO;
use Crater\DataObject\UserData;
use Crater\Helpers\General;

class AuxVaultData extends PaymentDO
{
    public function __construct(PaymentAccountData $paymentAccount, UserData $user, PaymentAmountData $amount, ?int $id = null)
    {
        //parent::__construct($paymentAccount, $user, $amount / 100);
        parent::__construct($paymentAccount, $user, $amount, $id);
    }

    public function getUser(): UserData
    {
        return $this->user;
    }

    public function getCreditCardData(): array
    {
        return [
            'ExpiryDate' => $this->paymentAccount->formatted_decrypted_expiration_date,
            'CardNumber' => $this->paymentAccount->decrypted_card_number,
            'Cvv' => $this->paymentAccount->decrypted_cvv,
        ];
    }

    public function getCreditCardDataForValidation(): array
    {
        return [
            'number' => $this->paymentAccount->decrypted_card_number,
            'expiryMonth' => $this->paymentAccount->decrypted_expiration_date->month ?? null,
            'expiryYear' => $this->paymentAccount->decrypted_expiration_date->year ?? null,
        ];
    }

    public function getCCPurchaseData(): array
    {
        return [
            'Amount' => $this->amount->getTotalMini(),
            'BillingAddress' => $this->paymentAccount->address_1,
            'BillingCity' => $this->paymentAccount->city ?? null,
            'BillingCountry' => $this->paymentAccount->country->code ?? null,
            'BillingCountryCode' => $this->paymentAccount->getPhoneCode(),
            'BillingCustomerName' => $this->paymentAccount->first_name,
            'BillingEmail' => $this->user->email,
            'BillingPhoneNumber' => $this->user->phone,
            'BillingPostalCode' => $this->paymentAccount->zip,
            'BillingState' => $this->paymentAccount->state->name ?? null,
            'CardNumber' => $this->paymentAccount->decrypted_card_number,
            'Cvv' => $this->paymentAccount->decrypted_cvv,
            'ExpiryDate' => $this->paymentAccount->decrypted_expiration_date->format('m/y'),
            'SuggestedMode' => 'Card',
            'ConvenienceFeeActive' => false,
            //---
            'shippingSameAsBilling' => true,
            'IsRecurring' => false,
            //'MerchantId'            => 'xxxxx-xxx-xxxxxx-xxxxxx-xxxx',
            'TransactionType' => '1',
            //'PaymentTokenization'   => true,
            'Description' => $this->paymentAccount->getDescription(),
            'ReferenceNo' => '81',
            'Message' => $this->paymentAccount->getDescription(),
            'RequestOrigin' => 'vt',
            'TipAmount' => 0,
            'Giftcard' => false,
        ];
    }

    public function getAchPurchaseData(): array
    {
        return [
            'Amount' => $this->amount->getTotalMini(),
            'BillingAddress' => $this->paymentAccount->address_1,
            'BillingCity' => $this->paymentAccount->city ?? null,
            'BillingCountry' => $this->paymentAccount->country->name ?? null,
            'BillingCountryCode' => $this->paymentAccount->country->code ?? null,
            'BillingCustomerName' => $this->paymentAccount->first_name,
            'BillingEmail' => $this->user->email,
            'BillingPhoneNumber' => $this->user->phone,
            'BillingPostalCode' => $this->paymentAccount->zip,
            'BillingState' => $this->paymentAccount->state->name ?? null,
            'account_number' => $this->paymentAccount->decrypted_account_number,
            'routing_number' => $this->paymentAccount->decrypted_routing_number,
            'SuggestedMode' => 'ACH',
            'ConvenienceFeeActive' => false,
            'TransctionType' => '1',
            //---
            'shippingSameAsBilling' => true,
            'IsRecurring' => false,
            'Description' => $this->paymentAccount->getDescription(),
            'ReferenceNo' => '81',
            'Message' => $this->paymentAccount->getDescription(),
            'RequestOrigin' => 'vt',
            'TipAmount' => 0,
            'Giftcard' => false,
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

    public function successResponse(TransactionResponseData $data): array
    {
        return [
            'success' => true,
            'payment_gateway' => 'AuxVault',
            'transaction_number' => $data->getTransactionId(),
            'amount' => $this->amount->getAmountMini(),
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
            'payment_gateway' => 'AuxVault',
            'transaction_number' => null,
            'amount' => $this->amount->amount,
            'customer_id' => $this->user->id,
            'description' => $this->paymentAccount->getDescription(),
            'error_description' => $error,
            'message' => $error,
            'code_error' => $code,
            'payment_paypal_id' => null,
            'invoice_id' => $this->paymentAccount->invoiceId,
        ];

    }

    public function saveChargeAux(TransactionResponseData $data): array
    {
        return [
            'transaction_id' => $data->getTransactionId(),
            'base_amount' => $data->baseAmount,
            'amount' => $this->amount->getTotalMini(),
            'fees' => $this->amount->totalFees,
            'card_number' => $data->cardNumber,
            'email' => $data->email,
            'address' => $data->address,
            'city' => $data->city,
            'state' => $data->state,
            'postal_code' => $data->postalCode,
            'country' => $data->country,
            'country_code' => $data->countryCode,
            'phone_number' => $data->phone,
            'expiry_date' => General::encrypt($data->expiryDate),
            'cvv' => General::encrypt($data->cvv),
            'transaction_type' => $data->transactionType,
            'invoice_id' => $this->paymentAccount->invoiceId,
            'user_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'ach_routing_number' => $this->paymentAccount->last4Digits($this->paymentAccount->routing_number),
            'ach_account_number' => $this->paymentAccount->last4Digits($this->paymentAccount->account_number),
            'aux_vault_setting_id' => $this->setting_id,
        ];

    }
}
