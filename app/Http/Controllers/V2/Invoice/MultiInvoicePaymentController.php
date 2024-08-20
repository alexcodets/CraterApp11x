<?php

namespace Crater\Http\Controllers\V2\Invoice;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\MultiInvoicePaymentRequest;
use Crater\Models\Invoice;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentGatewaysFee;
use Crater\Services\Payment\MultiPurposePaymentService;
use Symfony\Component\HttpFoundation\Response;

class MultiInvoicePaymentController extends Controller
{
    public function __invoke(MultiInvoicePaymentRequest $request)
    {

        //TODO: El orden sea de la mas vieja a las mas nueva.
        $invoices = Invoice::find($request->invoices);
        $totalToPay = $invoices->slice(0, -1)->sum('due_amount');
        \Log::debug($totalToPay);
        \Log::debug($request->amount);
        if ($request->amount <= $totalToPay) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => 'The amount to pay must be enough to pay at last a fraction of the last invoice selected.',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], 422);
        }

        $totalToPay = $invoices->sum('due_amount');

        if ($request->amount > $totalToPay) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => 'The amount to pay cannot be higher than the due amount of all the invoices to pay.',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], 422);
        }

        $account = PaymentAccountData::fromArray($request->all());
        $user = $invoices[0]->user;

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

            \Log::debug('Data:', [
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

        $account->setDescription('Payment for multiple invoices: '.implode(', ', $request->invoices));
        $user = UserData::fromModel($user);

        $service = new MultiPurposePaymentService($account, $user);
        $amount = new PaymentAmountData($request->amount, $request->has_fees, $request->fees);

        return $service->handleMultiple($invoices, $amount);

    }
}
