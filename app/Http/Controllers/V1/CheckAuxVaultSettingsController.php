<?php

namespace Crater\Http\Controllers\V1;

use Crater\Http\Requests\CheckAuxVaultSettingRequest;
use Crater\Models\User;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\AuxVault\AuxVaultPaymentService;

class CheckAuxVaultSettingsController
{
    public function __invoke(CheckAuxVaultSettingRequest $request, User $user)
    {

        try {
            $paymentAccount = $user->paymentAccount;
            if (is_null($paymentAccount)) {
                return response()->json(['error' => 'A payment Account is required to test the credentials'], 422);
            }
            $data = new AuxVaultData($paymentAccount, $user, 0);
            $array = $request->all();

            $response = AuxVaultPaymentService::handleCreditCard($data, 'array', $array);

            return response()->json();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }

    }
}
