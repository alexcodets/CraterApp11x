<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\Http\Controllers\Controller;
use Crater\Models\Payment;
use Illuminate\Http\Request;

class PaymentPdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Payment $payment)
    {
        // dd($payment);

        return $payment->getGeneratedPDFOrStream('payment');
    }
}
