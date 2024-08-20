<?php

namespace Crater\Services\Payment\AuxVault;

use Crater\Http\Integrations\AuxVault\AuxVaultConnector;
use Crater\Http\Integrations\AuxVault\Data\AuxVaultConfigData;
use Crater\Models\AuxVaultSetting;
use Exception;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\CreditCard;
use Throwable;

class AuxVaultPaymentService
{
    /** @throws Throwable */
    public static function handleCreditCard(AuxVaultData &$data, ?string $type = null, $extraData = []): TransactionResponseData
    {
        return self::handleAll($data, 'CC', $type, $extraData);
    }

    /**
     * @throws Throwable
     */
    public static function handleAll(AuxVaultData &$data, string $paymentMethod, ?string $type = null, $extraData = []): TransactionResponseData
    {
        if ($paymentMethod === 'CC') {
            $card = new CreditCard($data->getCreditCardDataForValidation());

            try {
                $card->validate();
            } catch (Throwable $th) {
                Log::error('There was a error while validating the credit card for AuxVault');
                Log::error($th);

                throw $th;
            }
        }

        try {
            $gateway = self::getConector($type, $extraData, $data);
        } catch (Throwable $th) {
            Log::error('There is not data to build de aux vault config');
            Log::error($th);

            throw $th;
        }

        switch ($paymentMethod) {
            case 'CC':
                $response = $gateway->purchaseWithCC($data->getCCPurchaseData());

                break;
            case 'ACH':
                $response = $gateway->purchaseWithAch($data->getAchPurchaseData());

                break;
            default:
                throw new Exception('Invalid method');
        }

        Log::debug('response aux vault service 57', [$response]);
        if (! isset($response['status'])) {
            Log::error($response->json());

            throw new Exception($response['message']);
        }
        if (isset($response['status']) && $response['status'] !== 'success') {
            Log::error($response->json());

            throw new Exception($response['message']);
        }

        Log::debug('Process End normally', [$response->json()]);

        return TransactionResponseData::fromResponseArray($response->json());

    }

    /**  @throws Throwable */
    public static function getConector(?string $type, $extraData, AuxVaultData &$data): AuxVaultConnector
    {
        switch ($type) {
            case 'config':
                $gateway = new AuxVaultConnector(AuxVaultConfigData::fromConfig());

                break;
            case 'array':
                $gateway = new AuxVaultConnector(AuxVaultConfigData::fromArray($extraData));

                break;
            case 'model':
            default:
                Log::debug('Log get user id'.$data->getUser()->id);
                $aux = AuxVaultSetting::where('default', true)->first();
                if (! $aux) {
                    throw new Exception('An active setting for AuxVault was not found.', 404);
                }
                Log::debug($aux);
                $data->setting_id = $aux->id;
                $gateway = new AuxVaultConnector(AuxVaultConfigData::fromModel($aux));

                break;
        }

        return $gateway;
    }

    /**  @throws Throwable */
    public static function handleAch(AuxVaultData &$data, ?string $type = null, $extraData = []): TransactionResponseData
    {
        return self::handleAll($data, 'ACH', $type, $extraData);
    }
}
