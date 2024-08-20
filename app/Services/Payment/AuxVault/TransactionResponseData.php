<?php

namespace Crater\Services\Payment\AuxVault;

class TransactionResponseData
{
    public float $baseAmount;

    public float $amount;

    public ?string $cardNumber;

    public string $email;

    public string $address;

    public ?string $city;

    public ?string $state;

    public ?string $postalCode;

    public ?string $country;

    public ?string $countryCode;

    public ?string $phone;

    public ?string $expiryDate;

    public ?string $cvv;

    public string $name;

    public ?string $transactionType;

    private ?string $transactionId;

    public ?string $accountNumber;

    public ?string $routingNumber;

    public ?string $status;

    public ?string $type;

    private ?int $invoiceId;

    public function __construct(
        string $transactionId,
        float $baseAmount,
        float $amount,
        ?string $cardNumber,
        string $email,
        string $name,
        string $address,
        ?string $city,
        ?string $state,
        string $postalCode,
        ?string $country,
        string $countryCode,
        ?string $phone,
        ?string $expiryDate,
        ?string $cvv,
        ?string $transactionType,
        ?string $accountNumber,
        ?string $routingNumber,
        ?string $status,
        ?string $type
    ) {
        $this->transactionId = $transactionId;
        $this->baseAmount = $baseAmount;
        $this->amount = $amount;
        $this->cardNumber = $cardNumber;
        $this->email = $email;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->postalCode = $postalCode;
        $this->country = $country;
        $this->countryCode = $countryCode;
        $this->phone = $phone;
        $this->expiryDate = $expiryDate;
        $this->cvv = $cvv;
        $this->name = $name;
        $this->transactionType = $transactionType;
        $this->invoiceId = null;
    }

    public static function fromResponseArray(array $data): self
    {
        return new self(
            $data['data']['TransactionId'],
            $data['data']['BaseAmount'],
            $data['data']['Amount'],
            $data['data']['CardNumber'] ?? null,
            $data['data']['BillingEmail'],
            $data['data']['BillingCustomerName'],
            $data['data']['BillingAddress'],
            $data['data']['BillingCity'],
            $data['data']['BillingState'],
            $data['data']['BillingPostalCode'],
            $data['data']['BillingCountry'],
            $data['data']['BillingCountryCode'],
            $data['data']['BillingPhoneNumber'],
            $data['data']['ExpiryDate'] ?? null,
            $data['data']['Cvv'] ?? null,
            $data['data']['TransactionType'] ?? null,
            $data['data']['AccountNumber'] ?? null,
            $data['data']['RoutingNumber'] ?? null,
            $data['data']['Status'] ?? null,
            $data['data']['Type'] ?? null
        );
    }

    public static function fromCCResponseArray(array $data): self
    {
        return new self(
            $data['data']['TransactionId'],
            $data['data']['BaseAmount'],
            $data['data']['Amount'],
            $data['data']['CardNumber'] ?? null,
            $data['data']['BillingEmail'],
            $data['data']['BillingCustomerName'],
            $data['data']['BillingAddress'],
            $data['data']['BillingCity'],
            $data['data']['BillingState'],
            $data['data']['BillingPostalCode'],
            $data['data']['BillingCountry'],
            $data['data']['BillingCountryCode'],
            $data['data']['BillingPhoneNumber'],
            $data['data']['ExpiryDate'],
            $data['data']['Cvv'],
            $data['data']['TransactionType'] ?? null,
            null,
            null,
            null,
            null
        );

    }

    public static function fromAchResponseArray(array $data): self
    {
        return new self(
            $data['data']['TransactionId'],
            $data['data']['BaseAmount'],
            $data['data']['Amount'],
            null,
            $data['data']['BillingEmail'],
            $data['data']['BillingCustomerName'],
            $data['data']['BillingAddress'],
            $data['data']['BillingCity'],
            $data['data']['BillingState'],
            $data['data']['BillingPostalCode'],
            $data['data']['BillingCountry'],
            $data['data']['BillingCountryCode'],
            $data['data']['BillingPhoneNumber'],
            null,
            null,
            $data['data']['TransactionType'] ?? null,
            $data['data']['AccountNumber'],
            $data['data']['RoutingNumber'],
            $data['data']['Status'],
            $data['data']['Type']
        );
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }
}
