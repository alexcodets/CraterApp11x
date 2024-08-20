<?php

namespace Crater\Services\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
use Crater\DataObject\UserData;
use Crater\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class MultiPurposeRechargeBalanceService
{
    public UserData $user;

    public bool $email;

    public int $tries;

    public int $maxTries;

    private MultiPurposePaymentService $service;

    public PaymentAccountData $data;

    public function __construct(UserData $user, MultiPurposePaymentService $service, PaymentAccountData $paymentAccountData)
    {
        $this->user = $user;
        $this->email = false;
        $this->maxTries = $user->customerConfig->auto_debit_attempts ?? 3;
        $this->tries = 0;
        $this->service = $service;
        $this->data = $paymentAccountData;
    }

    public function handle(PaymentAmountData $amount): array
    {
        try {
            return $this->rechargeBalance($amount);
        } catch (Throwable $th) {
            //throw $th;
            Log::channel('payment')->debug($th->getMessage());

            return [
                'success' => false,
                'payment_gateway' => 'AuxVault',
                'transaction_number' => null,
                'amount' => $amount,
                'customer_id' => $this->user->id,
                'description' => 'Charge to restore credit',
                'error_description' => $th->getMessage(),
                'message' => $th->getMessage(),
                'code_error' => $th->getCode(),
                'payment_paypal_id' => null,
                'invoice_id' => '',
            ];
        }
    }

    /**
     * Try to recharge the balance.
     *
     * @throws Exception
     * @throws Throwable
     */
    public function rechargeBalance(PaymentAmountData $amount): array
    {
        // if ($this->user->auto_replenish_amount == 0 || $this->user->auto_replenish_amount == null)
        // throw new Exception ('The Customer has no money and have auto replenish amount to zero');

        try {
            Log::channel('payment')->debug('Trying to recharge');
            $val = $this->recharge($amount);
            if (! $val['success']) {
                Log::channel('payment')->debug('It failed');
                Log::channel('payment')->debug($val['message'] ?? 'No message');
            }
        } catch (Throwable $th) {
            //throw $th;
            Log::channel('payment')->error('Error while recharging user credit');
            Log::channel('payment')->error($th->getMessage());
            Log::channel('payment')->error($th->getTraceAsString());
            $this->tries++;

            throw $th;
        }

        //Log::channel('payment')->debug($val);
        if ($val['success']) {
            $this->user->balance += $amount->getAmountMini();
            if ($this->user->id) {
                User::where('id', $this->user->id)->increment('balance', $amount->getAmountMini());
            } else {
                Log::channel('payment')->error('We could not find a user for the recharge');
            }

            Log::channel('payment')->debug('The recharge was a success');

        }

        return $val;

    }

    // procesa el cobro por el metodo de pago que tenga el cliente activado

    /**
     * @throws Exception
     */
    protected function recharge(PaymentAmountData $amount): array
    {
        return $this->service->rechargeBalance($amount);
    }
}
