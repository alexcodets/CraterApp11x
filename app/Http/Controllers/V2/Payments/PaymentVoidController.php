<?php

namespace Crater\Http\Controllers\V2\Payments;

use Crater\Authorize\Models\Authorize;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PaymentVoidRequest;
use Crater\Models\{AuxVault, BalanceCustomer, Expense, Invoice, Payment, PaymentDevolution, User};
use Crater\Services\Payment\Authorize\AuthorizeVoidService;
use Crater\Services\Payment\AuxVault\AuxVoidService;

class PaymentVoidController extends Controller
{
    public function __invoke(PaymentVoidRequest $request, Payment $payment)
    {

        // DISCLAIMER:
        // The void will be made manually by the admin or client before doing the current process.
        if (! $request->input('only_local', false)) {
            $response = $this->realVoid($payment);
        }

        if ($request->only_local) {
            $response = [
                'success' => true,
                'transaction_id' => $payment->transaction_id,
                'original_id' => $payment->transaction_id,
                'gateway' => 'Local',
                'description' => 'local void.',
            ];
        }

        $response['payment_id'] = $payment->id;


        if (! $response['success']) {
            return response()->json($response);
        }

        $payments = Payment::associatedPayments($payment)->get();
        if ($payments->isEmpty()) {
            $payments = collect($payment);
        }

        $payments->each(function ($item) use (&$response, $request) {
            $response['payments_id'][] = $item->id;
            if ($item->invoice_id) {
                PaymentDevolution::create([
                    'invoice_id' => $item->invoice_id,
                    'payment_method' => $item->paymentMethod->name ?? 'N/A',
                    'transaction_id' => $response['transaction_id'],
                    'date' => now(),
                    'amount' => $item->amount,
                    'payload' => (string)(json_encode([
                        'original_transaction_id' => $response['original_id'],
                        'transaction_id' => $response['transaction_id'],
                        'note' => $request['notes'],
                    ])),
                    'status' => 'void',
                ]);

            }

            $invoice = Invoice::find($item->invoice_id);

            $item->transaction_status = 'Void';
            $item->notes = $request['notes'];
            $item->applied_credit_customer = ! $invoice;
            $item->save();

            if ($invoice) {
                if ($invoice->total == $item->amount) {
                    $invoice->due_amount = $invoice->total;
                    $invoice->paid_status = Invoice::STATUS_UNPAID;
                } else {
                    $invoice->due_amount = $invoice->due_amount + $item->amount;
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
                if (! is_null($customer) && $customer->balance > $item->amount) {
                    $customer->balance = $customer->balance - ($item->amount / 100);
                    $customer->save();
                    $customer_balance->present_balance = $customer->balance - ($item->amount / 100);
                    $customer_balance->save();

                    BalanceCustomer::create([
                        'status' => 'A',
                        'present_balance' => $customer->balance,
                        'amount_op' => $item->amount / 100,
                        'amount_final' => $customer->balance + ($item->amount / 100),
                        'payment_id' => $item->id,
                        'user_id' => $customer->id,
                    ]);

                    if ($expense != null) {
                        $expense->delete();
                    }
                }
            }
        });

        return response()->json($response);
    }

    public function realVoid(Payment $payment): array
    {
        switch ($payment->payment_gateway) {
            case 'Authorize':
                $gateway = Authorize::findOrFail($payment->authorize_id);
                $service = new AuthorizeVoidService();
                $response = $service->handle($gateway);
                $response['original_id'] = $gateway->transaction_id;

                return $response;
            case 'Paypal':
                return [
                    'success' => false,
                    'gateway' => 'N/A',
                    'description' => 'Paypal\'s payment gateway may not be void.',
                ];
            case 'AuxVault':
                $gateway = AuxVault::findOrFail($payment->aux_vault_id);
                $service = new AuxVoidService();
                $response = $service->handle($gateway, $request['notes'] ?? null);
                $response['original_id'] = $gateway->transaction_id;

                return $response;
            default:
                return [
                    'success' => false,
                    'gateway' => 'N/A',
                    'description' => 'Payment Gateway no found',
                    'payment_id' => $payment->id,
                ];
        }
    }
}
