<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\Http\Requests\AuthorizePaymentRequest;
use Crater\Models\User;
use Crater\Services\Payment\Authorize\AuthorizePaymentService;
use Crater\Services\Payment\Authorize\PaymentAuthorizeDO;

class AuthorizePaymentController
{
    public function __invoke(AuthorizePaymentRequest $request)
    {
        #TODO: verificar como viene la data, el user puede ser anonimo.
        $payment = PaymentAccountData::fromArray($request->all());
        $data = new PaymentAuthorizeDO($payment, auth('sanctum')->user(), $request->amount);

        #TODO: logica para si es credit card o si es ach.
        $response = AuthorizePaymentService::handleCreditCard($data);
        $response['invoice_id'] = $this->invoiceID;
    }
}
