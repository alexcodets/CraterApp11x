<?php

namespace Crater\Services\Payment\AuxVault;

use Crater\Http\Integrations\AuxVault\AuxVaultConnector;
use Crater\Http\Integrations\AuxVault\Data\AuxVaultConfigData;
use Crater\Models\AuxVault;
use Crater\Models\AuxVaultSetting;
use Crater\Services\Payment\Traits\VoidTrait;

class AuxRefundService
{
    use VoidTrait;

    public function __construct()
    {
        $this->name = 'AuxVault';
    }

    public function handle(AuxVault $auxVault, int $amount, string $type = 'total', ?string $reason = null): array
    {
        // FullRefund / PartialRefund
        $aux = AuxVaultSetting::query()
            ->where('default', true)
            ->firstOrFail();

        $refId = 'ref'.time();
        $this->refId = $refId;

        $gateway = new AuxVaultConnector(AuxVaultConfigData::fromModel($aux));
        $response = $gateway->refund(array_filter([
            'TransactionFor' => $type === 'total' ? 'FullRefund' : 'PartialRefund',
            'TransactionId' => $auxVault->transaction_id,
            'Reason' => $reason,
            'Amount' => $amount,
        ]));

        $response = $response->json();
        if (($response['status'] ?? false) !== 'success') {
            return $this->errorResponse(500, $response['message'] ?? 'Unknown error', '');
        }

        return $this->successResponse(200, $response['message'], '', $response['data']['TransactionId']);

    }
}
