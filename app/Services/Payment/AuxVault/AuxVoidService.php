<?php

namespace Crater\Services\Payment\AuxVault;

use Crater\Http\Integrations\AuxVault\AuxVaultConnector;
use Crater\Http\Integrations\AuxVault\Data\AuxVaultConfigData;
use Crater\Models\AuxVault;
use Crater\Models\AuxVaultSetting;
use Crater\Services\Payment\Traits\VoidTrait;

class AuxVoidService
{
    use VoidTrait;

    public function __construct()
    {
        $this->name = 'AuxVault';
    }

    public function handle(AuxVault $auxVault, ?string $reason = null): array
    {
        $aux = AuxVaultSetting::query()
            ->where('default', true)
            ->firstOrFail();

        // Set the transaction's refId
        $refId = 'ref'.time();
        $this->refId = $refId;

        $gateway = new AuxVaultConnector(AuxVaultConfigData::fromModel($aux));
        $response = $gateway->void(array_filter([
            'TransactionFor' => 'Void',
            'TransactionId' => $auxVault->transaction_id,
            'Reason' => $reason,
        ]));

        $response = $response->json();
        \Log::debug($response);

        if (! isset($response['status'])) {
            if (isset($response['message']) && $response['message'] === 'Transaction Voided Successfully') {
                // El índice 'status' no existe y el índice 'message' es 'Transaction Voided Successfully'
                // Coloca aquí el código que se debe ejecutar en este caso
                return $this->successResponse(200, $response['message'], '', $response['data']['TransactionId']);
            }
        }

        if (($response['status'] ?? false) !== 'success') {
            return $this->errorResponse(500, $response['message'] ?? 'Unknown error', '');
        }

        return $this->successResponse(200, $response['message'], '', $response['data']['TransactionId']);

    }
}
