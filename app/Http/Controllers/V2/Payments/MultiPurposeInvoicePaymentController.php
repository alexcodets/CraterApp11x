<?php

namespace Crater\Http\Controllers\V2\Payments;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Http\Requests\AuthorizePaymentRequest;
use Crater\Models\Invoice;
use Crater\Services\Payment\MultiPurposeInvoicePaymentService;
use Log;

class MultiPurposeInvoicePaymentController
{
    public function __invoke(AuthorizePaymentRequest $request, Invoice $invoice)
    {
        $amount = $request->input('amount');
        Log::debug('request multipurpose payment Invoice controller ', ['request' => $request->all(), 'invoice' => $invoice]);
        $account = PaymentAccountData::fromArray($request->all());

        // TODO: User from invoice or from user_id from the request. (the second for recharge)
        // TODO: Payment User: should come from the request data or from the invoice
        // There are 2 type of user, the one that pay, and the one that receive the payment.
        // $user would be
        // User for who is being made the payment
        $user = $invoice->user;
        $user = UserData::fromModel($user);
        //User that is making the payment.
        //$user = UserData::fromArray($request->all());
        $amount = $amount ?? $invoice->due_amount;
        $amount = new PaymentAmountData($amount, $request->has_fees, $request->fees);

        $service = new MultiPurposeInvoicePaymentService($invoice, $user, $account);

        return $service->handle($invoice, $amount);

    }
}
