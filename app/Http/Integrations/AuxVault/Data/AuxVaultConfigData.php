<?php

namespace Crater\Http\Integrations\AuxVault\Data;

use Crater\Models\AuxVaultSetting;

class AuxVaultConfigData
{
    protected string $apiKey;

    public string $endpoint;

    private string $merchant_id;

    private string $currency;

    public function __construct(string $apiKey, string $endpoint, ?string $merchant_id, ?string $currency)
    {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;


        if (! empty($merchant_id)) {
            $this->merchant_id = $merchant_id;
        }
        if (! empty($currency)) {
            $this->currency = $currency;
        }
    }

    public static function fromConfig(): self
    {
        return (new self(config('services.aux_vault.api_key'), config('services.aux_vault.url'), config('services.aux_vault.merchant_id'), config('services.aux_vault.currency')));
    }

    public static function fromArray($data): self
    {
        return (new self($data['api_key'], $data['endpoint'], $data['merchant_id'], $data['currency']));
    }

    public static function fromModel(AuxVaultSetting $auxVault): self
    {
        return new self($auxVault->api_key_decrypted, $auxVault->endpoint, $auxVault->merchant_id_decrypted, $auxVault->currency);
    }

    public function getHeaderApiKey(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => $this->apiKey,
        ];
    }

    public function getMerchantId(): ?string
    {
        return $this->merchant_id ?? null;
    }
}
