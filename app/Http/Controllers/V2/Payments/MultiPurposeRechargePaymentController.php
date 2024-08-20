<?php

namespace Crater\Http\Controllers\V2\Payments;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Http\Requests\AuthorizePaymentRequest;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentGatewaysFee;
use Crater\Models\User;
use Crater\Services\Payment\MultiPurposePaymentService;
use Crater\Services\Payment\MultiPurposeRechargeBalanceService;
use Log;
use Symfony\Component\HttpFoundation\Response;

class MultiPurposeRechargePaymentController
{
    public function __invoke(AuthorizePaymentRequest $request)
    {
        Log::debug('request multipurpose payment Recharge controller ', ['request' => $request->all()]);
        $account = PaymentAccountData::fromArray($request->all());

        // TODO: User from invoice or from user_id from the request. (the second for recharge)
        // TODO: Payment User: should come from the request data or from the invoice
        // There are 2 type of user, the one that pay, and the one that receive the payment.
        // $user would be
        // User for who is being made the payment
        $user = User::findOrFail($request->input('user_id'));
        $user = UserData::fromModel($user);

        if ($request->input('has_fees', false)) {

            $count = PaymentGatewaysFee::whereIn('id', $request->fees)->count();

            if ($count != count($request->fees)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => 'The payment fees id are not valid.',
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], 422);
            }

            if ($account->payment_gateway_id) {
                $gate = PaymentGateways::where('company_id', $user->company_id)
                    ->where('status', 'A')
                    ->where('id', $account->payment_gateway_id)
                    ->first();
            } else {
                $gate = PaymentGateways::where('company_id', $user->company_id)
                    ->where('default', 1)
                    ->where('status', 'A')
                    ->first();
            }

            if (! $gate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => 'Payment gateway not found.',
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], 422);
            }

            \Log::debug("libea 82");
            \Log::debug($request->fees);
            \Log::debug($gate->name);

            $count = PaymentGatewaysFee::whereIn('id', $request->fees)
                ->where('payment_gateway', $gate->name)->count();

            Log::debug('Data:', [
                'count' => $count,
                'gate' => $gate->toArray(),
            ]);

            if ($count != count($request->fees)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data' => 'All the payment fees must be from the same payment gateway type.',
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], 422);
            }

        }

        //User that is making the payment.
        //$user = UserData::fromArray($request->all());
        $paymentService = new MultiPurposePaymentService($account, $user);
        $amount = new PaymentAmountData($request->input('amount'), $request->has_fees, $request->fees);

        $balanceService = new MultiPurposeRechargeBalanceService($user, $paymentService, $account);

        return $balanceService->handle($amount);
    }
}
