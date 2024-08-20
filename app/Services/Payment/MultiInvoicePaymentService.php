<?php

namespace Crater\Services\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MultiInvoicePaymentService
{
    private UserData $user;

    private MultiPurposePaymentService $paymentService;

    private Collection $invoices;

    private PaymentAccountData $paymentAccountData;

    private int $total;

    /**
     * @var mixed
     */
    private $dueAmount;

    public function __construct(Collection $invoices, UserData $user, PaymentAccountData $paymentAccountData, int $total)
    {
        $this->invoices = $invoices;
        $this->user = $user;
        $this->paymentService = new MultiPurposePaymentService($paymentAccountData, $user);
        $this->paymentAccountData = $paymentAccountData;
        $this->total = $total;
        $this->dueAmount = $invoices->sum('due_amount');
    }

    /** @throws \Throwable */
    public function handle(): array
    {
        try {

            DB::beginTransaction();
            Log::channel('payment')->debug('Invoices: ', [
                'ids' => $this->invoices->pluck('id')->toArray(),
                'due_amount' => $this->dueAmount,
            ]);

            Log::channel('payment')->debug('Pay with cash (CC or ACH)');
            $response = $this->payWithCash($amount);

            DB::commit();
        } catch (Throwable $th) {
            try {
                //throw $th;
                Log::channel('payment')->debug('Error in the process rolling back');
                DB::rollback();
                Log::channel('payment')->error($th->getMessage());

                return $this->failResponse($invoice, $th->getMessage(), $th->getCode(), $amount);
            } catch (Throwable $thr) {
                Log::channel('payment')->error('*** Error While rolling back, the data could be corrupted ***');
                Log::channel('payment')->error('*** Error Error Error Error Warning ****');

                return $this->failResponse($invoice, $thr->getMessage(), $thr->getCode(), $amount);
            }
        }

        return $response;

    }
}
