<?php

namespace Crater\Http\Controllers\V2\Payments;

use Crater\Authorize\Models\Authorize;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PaymentRefundRequest;
use Crater\Models\AuxVault;
use Crater\Models\BalanceCustomer;
use Crater\Models\Expense;
use Crater\Models\FailedPaymentHistory;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\PaymentDevolution;
use Crater\Models\User;
use Crater\Services\Payment\Authorize\AuthorizeRefundService;
use Crater\Services\Payment\AuxVault\AuxRefundService;
use Symfony\Component\HttpFoundation\Response;

class PaymentRefundController extends Controller
{
    public function __invoke(PaymentRefundRequest $request, Payment $payment): array
    {
        if ($payment->created_at > now()->subHours(24)) {
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => 'The payment needs to be more than 24 hours old.',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], 422);
        }

        if ($payment->created_at < now()->subDays(179)) {
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => 'The payment must not exceed 72 days from the transaction date.',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], 422);
        }

        $amount = $request->type === 'total' ? $payment->amount / 100 : $request->amount;

        switch ($payment->payment_gateway) {
            case 'Authorize':
                $gateway = Authorize::findOrFail($payment->authorize_id);
                $service = new AuthorizeRefundService();
                $response = $service->handle($gateway, $request, $amount);

                break;
            case 'Paypal':
                return [
                    'success' => false,
                    'gateway' => 'N/A',
                    'description' => 'Paypal\'s payment gateway may not be void.',
                ];
            case 'AuxVault':
                $gateway = AuxVault::findOrFail($payment->aux_vault_id);
                $service = new AuxRefundService();
                $response = $service->handle($gateway, $amount, $request->input('type', 'total'), $request['notes'] ?? null);

                break;
            default:
                return [
                    'success' => false,
                    'gateway' => 'N/A',
                    'description' => 'Payment Gateway no found',
                    'payment_id' => $payment->id,
                ];
        }

        $response['payment_id'] = $payment->id;

        if (! $response['success']) {
            FailedPaymentHistory::create([
                'payment_gateway' => $payment->payment_gateway,
                'transaction_number' => $payment->transaction_id,
                'date' => now(),
                'amount' => $payment->amount,
                'customer_id' => $payment->user_id,
                'description' => $response['description'],
                'invoice_id' => $payment->id,
                'error_description' => $response['description'],
                'type' => 'Refund',
            ]);

            return $response;
        }

        $payments = Payment::associatedPayments($payment)->get();
        if ($payments->isEmpty()) {
            $payments = collect($payment);
        }

        $payments->each(function ($item) use (&$response, $request, $gateway) {
            $invoice = $item->invoice;
            if ($item->invoice_id) {
                PaymentDevolution::create([
                    'invoice_id' => $item->invoice_id,
                    'payment_method' => $item->paymentMethod->name,
                    'transaction_id' => $response['transaction_id'],
                    'date' => now(),
                    'amount' => $request->amount,
                    'payload' => (string)(json_encode([
                        'original_transaction_id' => $gateway->transaction_id,
                        'transaction_id' => $response['transaction_id'],
                        'note' => $request['notes'] ?? null,
                        'ref_id' => $response['ref_id'] ?? null,
                    ])),
                    'status' => PaymentDevolution::STATUS_REFUNDED,
                ]);
            }

            $item->transaction_status = PaymentDevolution::STATUS_REFUNDED;
            $item->notes = $request['notes'];
            $item->applied_credit_customer = ! $invoice;
            $item->save();

            if ($invoice) {
                if ($invoice->total == $request->amount) {
                    $invoice->due_amount = $invoice->total;
                    $invoice->paid_status = Invoice::STATUS_UNPAID;
                } else {
                    $invoice->due_amount = $invoice->due_amount + $request->amount;
                    $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
                }
                $invoice->status = Invoice::STATUS_SENT;
                $invoice->save();
            } else {
                $customer = User::where('id', $item->user_id)->first();
                $customer_balance = BalanceCustomer::where('user_id', $item->user_id)->first();
                $expense = Expense::where('payment_id', $item->id)->first();

                //TODO: Si hace un void pero gasto parte del saldo, no se descuenta..
                //$payment->applied_credit_customer
                if (! is_null($customer) && $customer->balance > $request->amount) {
                    $customer->balance = $customer->balance - ($request->amount / 100);
                    $customer->save();
                    $customer_balance->present_balance = $customer->balance - ($request->amount / 100);
                    $customer_balance->save();

                    BalanceCustomer::create([
                        'status' => 'A',
                        'present_balance' => $customer->balance,
                        'amount_op' => $request->amount / 100,
                        'amount_final' => $customer->balance + ($request->amount / 100),
                        'payment_id' => $item->id,
                        'user_id' => $customer->id,
                    ]);

                    if ($expense != null) {
                        $expense->delete();
                    }
                }
            }
        });

        return $response;

    }
}
