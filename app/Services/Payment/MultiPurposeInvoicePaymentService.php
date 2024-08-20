<?php

namespace Crater\Services\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MultiPurposeInvoicePaymentService
{
    private MultiPurposePaymentService $paymentService;

    private UserData $user;

    private Invoice $invoice;

    public function __construct(Invoice $invoice, UserData $user, PaymentAccountData $paymentAccountData)
    {
        $this->invoice = $invoice;
        $this->user = $user;
        $this->paymentService = new MultiPurposePaymentService($paymentAccountData, $user);
    }

    public function handle(Invoice $invoice, PaymentAmountData $amount): array
    {
        try {

            DB::beginTransaction();
            Log::channel('payment')->debug("Invoice: {$invoice->id}");
            Log::channel('payment')->debug("Invoice due amount: {$invoice->due_amount}");

            Log::channel('payment')->debug('Pay with cash (CC or ACH)');
            $response = $this->payWithCash($amount);

            DB::commit();
        } catch (Throwable $th) {
            try {
                //throw $th;
                Log::channel('payment')->debug('Error in the process rolling back');
                DB::rollback();
                Log::channel('payment')->error($th->getMessage());

                return $this->failResponse($invoice, $th->getMessage(), $th->getCode(), $amount->amount);
            } catch (Throwable $thr) {
                Log::channel('payment')->error('*** Error While rolling back, the data could be corrupted ***');
                Log::channel('payment')->error('*** Error Error Error Error Warning ****');

                return $this->failResponse($invoice, $thr->getMessage(), $thr->getCode(), $amount->amount);
            }
        }

        return $response;

    }

    public function failResponse(Invoice $invoice, string $error, string $code, int $amount): array
    {
        return [
            'success' => false,
            'payment_gateway' => null,
            'transaction_number' => null,
            'amount' => $amount,
            'customer_id' => $this->user->id,
            'description' => 'Invoice Payment',
            'error_description' => $error,
            'message' => $error,
            'code_error' => $code,
            'payment_paypal_id' => null,
            'invoice_id' => $invoice->id,
        ];

    }

    /**
     * @throws Throwable
     */
    public function payWithCash(PaymentAmountData $amount): array
    {

        try {
            Log::channel('payment')->debug('Trying to Pay');
            $val = $this->paymentService->payInvoice($this->invoice, $amount);
            if (! $val['success']) {
                Log::channel('payment')->debug('It failed');
                Log::channel('payment')->debug($val['message'] ?? 'No message');
            }
        } catch (Throwable $th) {
            //throw $th;
            Log::channel('payment')->error('Error while Paying with cash');
            Log::channel('payment')->error($th->getMessage());
            Log::channel('payment')->error($th->getTraceAsString());

            throw $th;
        }

        if ($val['success']) {
            $this->invoice->due_amount = 0;
            $this->invoice->paid_status = Invoice::STATUS_PAID;
            $this->invoice->status = Invoice::STATUS_COMPLETED;
            $this->invoice->save();
        }

        return $val;
    }

    public function getPrefix()
    {
        return CompanySetting::where('option', 'payment_prefix')
            ->where('company_id', $this->user->company_id)->first()->value ?? 'PAY';
    }
}
