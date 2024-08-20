<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Http\Requests\AuthorizePaymentRequest;

class AuxVaultPaymentController
{
    public function __invoke(AuthorizePaymentRequest $request)
    {
        $payment = PaymentAccountData::fromArray($request->all());
        $user = UserData::fromModel(auth('sanctum')->user());
    }
}
