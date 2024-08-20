<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PaymentVoidRequest;
use Crater\Models\Payment;

class AuxVoidController extends Controller
{
    public function __invoke(PaymentVoidRequest $request, Payment $payment)
    {

    }
}
