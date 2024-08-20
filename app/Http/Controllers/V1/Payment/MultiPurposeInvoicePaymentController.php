<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Http\Requests\AuthorizePaymentRequest;
use Crater\Models\Invoice;
use Crater\Models\User;
use Crater\Services\Payment\MultiPurposeInvoicePaymentService;
use Crater\Services\Payment\MultiPurposePaymentService;
use Crater\Services\Payment\MultiPurposeRechargeBalanceService;
use Log;

class MultiPurposeInvoicePaymentController
{
    public function __invoke(AuthorizePaymentRequest $request, ?Invoice $invoice)
    {
        $amount = $request->input('amount');
        Log::debug('request multipurpose payment controller ', ['request' => $request->all(), 'invoice' => $invoice]);
        $account = PaymentAccountData::fromArray($request->all());


        // TODO: User from invoice or from user_id from the request. (the second for recharge)
        // TODO: Payment User: should come from the request data or from the invoice
        // There are 2 type of user, the one that pay, and the one that receive the payment.
        // $user would be
        // User for who is being made the payment
        $user = $invoice ? $invoice->user : User::findOrFail($request->input('user_id'));
        $user = UserData::fromModel($user);
        //User that is making the payment.
        //$user = UserData::fromArray($request->all());
        $paymentService = new MultiPurposePaymentService($account, $user);

        if ($invoice) {
            $amount = $amount ?? $invoice->due_amount;
            $amount = new PaymentAmountData($amount, $request->has_fees, $request->fees);
            $service = new MultiPurposeInvoicePaymentService($invoice, $user, $account);

            return $service->handle($invoice, $amount);
        }

        $amount = new PaymentAmountData($amount, $request->has_fees, $request->fees);
        $balanceService = new MultiPurposeRechargeBalanceService($user, $paymentService, $account);

        return $balanceService->handle($amount);
    }
}
