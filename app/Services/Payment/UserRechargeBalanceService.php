<?php

namespace Crater\Services\Payment;

use Crater\Models\CustomerConfig;
use Crater\Models\PaymentAccount;
use Crater\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserRechargeBalanceService
{
    public User $user;

    public bool $email;

    public int $tries;

    public int $maxTries;

    private PaymentService $service;

    public function __construct(User $user, PaymentService $service)
    {
        $this->user = $user;
        $this->email = false;
        $this->maxTries = $user->customerConfig->auto_debit_attempts;
        $this->tries = 0;
        $this->service = $service;
    }

    public function handle(int $amount): bool
    {
        try {
            $this->rechargeBalance($amount);

            return true;
        } catch (Throwable $th) {
            //throw $th;
            Log::debug($th->getMessage());

            return false;
        }
    }

    /**
     * Try to recharge the balance.
     *
     * @throws Exception
     * @throws Throwable
     */
    protected function rechargeBalance($amount): bool
    {
        if (! $this->user->auto_debit) {
            throw new Exception('AutoDebit is false');
        }
        if ($this->user->status_payment != 'prepaid') {
            throw new Exception('The user is not prepaid');
        }
        if ($this->user->minimun_balance <= $this->user->balance) {
            throw new Exception('The recharge is unnecessary, the user still have enough balance');
        }
        if ($this->user->auto_replenish_amount == 0 || $this->user->auto_replenish_amount == null) {
            throw new Exception('The Customer has no money and have auto replenish amount to zero');
        }
        if ($this->tries >= $this->maxTries) {
            throw new Exception('Max tries already reach');
        }

        $isEnableAutoDebit = CustomerConfig::where("customer_id", $this->user->id)
            ->where('enable_auto_debit', '=', 1)
            ->count();
        $isEnableApplyCredit = CustomerConfig::where("customer_id", $this->user->id)
            ->where('auto_apply_credits', '=', 1)
            ->count();
        $paymentAccount = PaymentAccount::with('country', 'state')->where("client_id", $this->user->id)
            ->where("main_account", 1)->where("status", "A")->whereNull('deleted_at')->first();
        /* @var PaymentAccount $paymentAccount */

        if ($isEnableAutoDebit == 0) {
            throw new Exception('The auto debit customer config is disabled');
        }
        if ($isEnableApplyCredit == 0) {
            throw new Exception('The option for apply credit is not active');
        }
        if ($paymentAccount == null) {
            throw new Exception('There is not Default Payment Account');
        }

        try {
            do {
                Log::debug('Trying to recharge');
                $val = $this->recharge($amount);
                if (! $val['success']) {
                    Log::debug('It failed');
                    Log::debug($val['message'] ?? 'No message');
                    $this->tries++;
                }
            } while (! $val['success'] && $this->tries < $this->maxTries);
        } catch (Throwable $th) {
            //throw $th;
            Log::error('Error while recharging user credit');
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
            $this->tries++;

            throw $th;
        }

        //Log::debug($val);
        if ($val['success']) {
            $this->user->balance += $amount / 100;
            $this->user->save();
            $this->user->refresh();

            Log::debug('The recharge was a success');

            return true;

        }
        $this->tries++;

        throw new Exception('Max tries reached');

    }

    // procesa el cobro por el metodo de pago que tenga el cliente activado

    /**
     * @throws Exception
     */
    protected function recharge(int $amount): array
    {
        return $this->service->rechargeBalance($amount);
    }
}
