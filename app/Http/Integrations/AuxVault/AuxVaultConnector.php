<?php

namespace Crater\Http\Integrations\AuxVault;

use Crater\Http\Integrations\AuxVault\Data\AuxVaultConfigData;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AuxVaultConnector
{
    protected AuxVaultConfigData $data;

    public function __construct(AuxVaultConfigData $data)
    {
        $this->data = $data;
    }

    public function purchase(array $data): Response
    {
        $data['MerchantId'] = $this->data->getMerchantId();

        return Http::withHeaders($this->data->getHeaderApiKey())
            ->post("{$this->data->endpoint}transaction", $data);

    }

    public function purchaseWithCC(array $data): Response
    {
        $data['MerchantId'] = $this->data->getMerchantId();

        return Http::withHeaders($this->data->getHeaderApiKey())
            ->post("{$this->data->endpoint}transaction", $data);

    }

    public function purchaseWithAch(array $data): Response
    {
        $data['MerchantId'] = $this->data->getMerchantId();

        return Http::withHeaders($this->data->getHeaderApiKey())
            ->post("{$this->data->endpoint}transaction", $data);

    }

    public function void(array $data): Response
    {
        $data['MerchantId'] = $this->data->getMerchantId();

        return Http::withHeaders($this->data->getHeaderApiKey())
            ->post("{$this->data->endpoint}void", $data);

    }

    public function refund(array $data): Response
    {
        $data['MerchantId'] = $this->data->getMerchantId();

        return Http::withHeaders($this->data->getHeaderApiKey())
            ->post("{$this->data->endpoint}refund", $data);

    }
}
