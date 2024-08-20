<?php

namespace Crater\DataObject;

use Carbon\Carbon;
use Crater\Models\Country;
use Crater\Models\PaymentAccount;
use Crater\Models\State;
use Illuminate\Support\Facades\Crypt;
use Log;

class PaymentAccountData
{
    public string $first_name;

    public string $last_name;

    public int $country_id;

    public int $state_id;

    public string $city;

    public string $address_1;

    public ?string $address_2;

    public ?string $zip;

    public ?string $payment_account_type;

    public ?string $card_number;

    public ?string $credit_card;

    public ?string $cvv;

    public ?string $expiration_date;

    public ?string $ACH_type;

    public ?string $account_number;

    public ?string $routing_number;

    public ?string $num_check;

    public string $status;

    public ?int $client_id;

    public int $company_id;

    public ?string $created_at;

    public ?string $bank_name;

    public ?bool $main_account;

    public ?string $decrypted_card_number;

    public ?Carbon $decrypted_expiration_date;

    public ?string $decrypted_formatted_expiration_date;

    public ?string $decrypted_cvv;

    public ?string $decrypted_ach_type;

    public ?string $decrypted_routing_number;

    public ?string $decrypted_account_number;

    public ?string $decrypted_bank_name;

    public ?string $partially_block_card_number;

    public ?string $formatted_decrypted_expiration_date;

    public ?int $id;

    public ?int $payment_gateway_id;

    public ?int $payment_method_id;

    public ?string $email;

    public ?string $invoiceNumber;

    public ?int $invoiceId;

    private ?string $name_on_account;

    public ?Country $country;

    public ?State $state;

    private ?string $description = null;

    public function __construct(
        string  $first_name,
        string  $last_name,
        int     $country_id,
        int     $state_id,
        string  $city,
        ?string $email,
        ?string $address_1,
        ?string $address_2,
        ?string $zip,
        ?string $payment_account_type,
        ?string $card_number,
        ?string $credit_card,
        ?string $cvv,
        ?string $expiration_date,
        ?string $ACH_type,
        ?string $account_number,
        ?string $routing_number,
        ?string $num_check,
        ?string $status,
        ?int    $client_id,
        ?int    $company_id,
        ?string $created_at,
        ?string $bank_name,
        ?bool   $main_account,
        ?int    $id,
        ?int    $payer_company_id,
        ?int    $payment_gateway_id,
        ?int    $payment_method_id,
        ?string $name_on_account
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->country_id = $country_id;
        $this->state_id = $state_id;
        $this->city = $city;
        $this->address_1 = $address_1;
        $this->address_2 = $address_2;
        $this->zip = $zip;
        $this->payment_account_type = $payment_account_type;
        $this->card_number = $card_number;
        $this->credit_card = $credit_card;
        $this->cvv = $cvv;
        $this->expiration_date = $expiration_date;
        $this->ACH_type = $ACH_type;
        $this->account_number = $account_number;
        $this->routing_number = $routing_number;
        $this->num_check = $num_check;
        $this->status = $status;
        $this->client_id = $client_id;
        $this->company_id = $company_id;
        $this->created_at = $created_at;
        $this->bank_name = $bank_name;
        $this->main_account = $main_account;
        $this->decrypted_card_number = $this->card_number ? $this->decryptField($this->card_number) : null;
        $date = $this->expiration_date ? $this->decryptField($this->expiration_date) : null;

        $this->decrypted_expiration_date = $date ? Carbon::createFromFormat('Y-m', $date) : null;
        //$this->decrypted_formatted_expiration_date = $this->decrypted_expiration_date ? $this->decrypted_expiration_date->format('Y-m') : null;
        $this->decrypted_formatted_expiration_date = $this->decrypted_expiration_date ? $date : null;
        $this->decrypted_cvv = $this->cvv ? $this->decryptField($this->cvv) : null;
        $this->decrypted_ach_type = $this->ACH_type ? $this->decryptField($this->ACH_type) : null;
        $this->decrypted_routing_number = $this->routing_number ? $this->decryptField($this->routing_number) : null;
        $this->decrypted_account_number = $this->account_number ? $this->decryptField($this->account_number) : null;
        $this->decrypted_bank_name = $this->bank_name ? $this->decryptField($this->bank_name) : null;
        $this->partially_block_card_number = null;
        if ($this->decrypted_card_number != null) {
            $cardNumber = substr($this->decrypted_card_number, -4);
            $this->partially_block_card_number = "************{$cardNumber}";
        }
        $this->formatted_decrypted_expiration_date = $this->decrypted_expiration_date ? $date : null;

        $this->id = $id;
        if ($this->id) {
            Log::debug('Is from model');
        }
        $this->payment_gateway_id = $payment_gateway_id;
        $this->payment_method_id = $payment_method_id;
        $this->email = $email;
        $this->invoiceId = null;
        $this->invoiceNumber = null;
        $this->name_on_account = $name_on_account;
        $this->country = Country::find($country_id);
        $this->state = State::find($state_id);
    }

    public static function fromModel(PaymentAccount $model): self
    {
        return new self(
            $model->first_name,
            $model->last_name,
            $model->country_id,
            $model->state_id,
            $model->city,
            null,
            $model->address_1,
            $model->address_2,
            $model->zip,
            $model->payment_account_type ?? null,
            $model->card_number,
            $model->credit_card,
            $model->cvv,
            $model->expiration_date,
            $model->ACH_type,
            $model->account_number,
            $model->routing_number,
            $model->num_check,
            $model->status,
            $model->client_id,
            $model->company_id,
            $model->created_at,
            $model->bank_name,
            $model->main_account,
            $model->id,
            null,
            null,
            null,
            $model->first_name
        );

    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['first_name'] ?? null,
            $array['last_name'] ?? null,
            $array['country_id'] ?? null,
            $array['state_id'] ?? null,
            $array['city'] ?? null,
            $array['email'] ?? null,
            $array['address_1'] ?? null,
            $array['address_2'] ?? null,
            $array['zip'] ?? null,
            $array['payment_account_type'] ?? null,
            $array['card_number'] ?? null,
            $array['credit_card'] ?? null,
            $array['cvv'] ?? null,
            $array['expiration_date'] ?? null,
            $array['ACH_type'] ?? null,
            $array['account_number'] ?? null,
            $array['routing_number'] ?? null,
            $array['num_check'] ?? null,
            $array['status'] ?? 'A',
            $array['user_id'] ?? null,
            $array['company_id'] ?? null,
            $array['created_at'] ?? null,
            $array['bank_name'] ?? null,
            $array['main_account'] ?? null,
            $array['payer_id'] ?? null,
            $array['payer_company_id'] ?? null,
            $array['payment_gateway_id'] ?? null,
            $array['payment_method_id'] ?? null,
            $array['nameOnAccount'] ?? null
        );

    }

    private function decryptField(string $field): ?string
    {
        if ($field && strlen($field) > 35) {
            return Crypt::decryptString($field);
        }

        return $field;
    }

    public function setInvoiceId(?int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function setDescription(?string $text): self
    {
        $this->description = $text;

        return $this;
    }

    public function getDescription(): ?string
    {
        if ($this->description) {
            return $this->description;
        }

        return $this->invoiceId ? "Payment for Invoice number: {$this->invoiceNumber}" : 'Charge to restore credit';
    }

    public function last4Digits(?string $value = null): ?string
    {
        if (is_null($value)) {
            return $value;
        }

        return substr($value, -4);
    }

    public function getNameOnAccount(): ?string
    {
        //return substr($this->first_name, 0, 21);
        return $this->name_on_account;
    }

    public function getExpirationDateMonth(): int
    {
        return substr($this->expiration_date, 0, 2);
    }

    public function getExpirationDateYear(): int
    {
        return substr($this->expiration_date, 3, 2);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getPhoneCode(): string
    {
        if ($this->country->phonecode ?? null) {
            return '+'.$this->country->phonecode;
        }

        return '';

    }
}
