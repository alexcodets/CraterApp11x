<?php

namespace Crater\Http\Controllers\V2\Payments;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\PaymentResource;
use Crater\Models\Payment;

class PaymentsAssociatedController extends Controller
{
    public function __invoke(Payment $payment)
    {
        return response()->json([
           'data' => PaymentResource::Collection(Payment::associatedPayments($payment)->get()),
        ]);
    }
}
