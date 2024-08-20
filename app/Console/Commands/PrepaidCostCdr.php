<?php

namespace Crater\Console\Commands;

use Cache;
use Crater\Events\LowBalanceEvent;
use Crater\Exceptions\PrepaidInsufficientBalance;
use Crater\Exceptions\PrepaidTotalCostCannotBeZero;
use Crater\Models\BalanceCustomer;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CallDetailRegisterTotalsHistory;
use Crater\Models\CallHistoryIndi;
use Crater\Models\CustomerConfig;
use Crater\Models\PaymentAccount;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Services\Payment\PaymentService;
use Crater\Services\Payment\UserRechargeBalanceService;
use Crater\Traits\SendEmailsTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

class PrepaidCostCdr extends Command
{
    use SendEmailsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepaid:cdrcharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculo de cdr';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Throwable
     */
    public function handle(): int
    {
        Log::debug('-------Calculo Prepaid cdr inicio----------');

        //TODO: Comprobar si todos los addresses son necesarios
        // Verificar que campos son necesarios, esta carga es algo pesada.

        $time = now()->format('Y-m-d');
        $alreadyProcessed = Cache::get("prepaidCdr-{$time}", function () {
            return [];
        });

        //Deleted user are not selected unless specified so is unnecessary to add deleted at that.
        $customers = User::customer()
            ->customerActive()
            ->prepaid()
            ->whereNotIn('id', $alreadyProcessed)
            ->whereHas('pbxServices', function (Builder $query) {
                $query->where('status', 'A');
            })->with([
                'pbxServices' => function ($query) {
                    $query->where('status', 'A')->with('taxes');
                },
            ])->with('addresses')->get();

        Cache::put("prepaidCdr-{$time}", $customers->pluck('id')->merge($alreadyProcessed)->toArray(), 1200); // 60 * 20

        foreach ($customers as $customer) {
            $this->process($customer);
        }

        Log::debug('-------Calculo Prepaid cdr fin----------');

        return 0;

    }

    /**
     *
     * Get The totals amount for the user.
     *
     * @throws PrepaidTotalCostCannotBeZero
     * @throws PrepaidInsufficientBalance
     */
    public function getTotals(User $customer, CallDetailRegisterTotal $cdrTotal, PbxServices $service): array
    {
        //Log::debug("Getting Totals");
        //Base values;
        $subTotal = $cdrTotal->exclusive_cost - $cdrTotal->exclusive_cost_paid; //bruto/price2

        if ($subTotal <= 0) {
            throw new PrepaidTotalCostCannotBeZero(__('exception.commands.prepaid.total_cannot_be_zero', ['total' => $subTotal]));
        }

        //Log::debug("Totals Before Taxes");

        [$taxes, $taxTotal] = $this->getTaxesTotal($service, $subTotal);
        $totalWithTax = $subTotal + $taxTotal;

        if ($totalWithTax <= 0) {
            throw new PrepaidTotalCostCannotBeZero('The ammount to deduct must be positive and > o, it cannot be: '.$totalWithTax);
        }

        //For global ammount, how would be paid in total after the process (wiouth tax)
        $globalTotalPaid = $cdrTotal->exclusive_cost;

        //Log::debug("Getting Totals After Global");
        if ($customer->balance < $totalWithTax) {

            $subTotal = $customer->balance - $taxTotal;
            if ($subTotal <= 0) {
                //the taxes are bigger than the balance...
                throw new PrepaidInsufficientBalance('Customer Balance to low for transaction');
            }
            [$taxes, $taxTotal] = $this->getTaxesTotal($service, $subTotal);
            $totalWithTax = $subTotal + $taxTotal;

            if ($totalWithTax > $customer->balance) {
                //the taxes are bigger than the balance...
                throw new PrepaidInsufficientBalance('Customer Balance to low even discounting the tax');
            }
            $globalTotalPaid = $cdrTotal->exclusive_cost_paid + ($subTotal);
        }

        //Log::debug("Totals Before return");
        return [$subTotal, $taxTotal, $totalWithTax, $globalTotalPaid, $taxes];

    }

    /**
     * @throws Exception
     */
    public function checkLowBalance(User $customer): bool
    {
        if ($customer->auto_replenish_amount > 0) {
            $response = $this->rechargeUserCredit($customer);
            if ($response['success']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Throwable
     */
    public function process(User $customer)
    {
        //Log::debug("Procesing Customer: {$customer->id}");
        $balanceCustomer = new BalanceCustomer();
        $balanceCustomer->status = 'D';
        $balanceCustomer->present_balance = $customer->balance; // el balance que tiene el cliente antes de la operacion o previo
        $balanceCustomer->amount_op = 0; // acumulado de lo cobrado
        $balanceCustomer->amount_final = 0; // se resta el balance dle customer y el balance2 de lo cobrado

        //For new costumer i guess.
        if ($customer->balance == 0) {
            if ($customer->auto_replenish_amount == 0) {
                //Log::debug("The Customer has no money and have auto replenish disabled");
                return;
            }
            $response = $this->checkLowBalance($customer);
            if ($response === false) {
                //Log::debug("The Customer has no money");
                return;
            }
            $customer->refresh();
        }

        foreach ($customer->pbxServices as $service) {
            //Log::debug("Procesando Servicio: {$service->id}");
            $cdrTotals = CallDetailRegisterTotal::where('pbx_services_id', $service->id)
                ->whereColumn('exclusive_cost', '>', 'exclusive_cost_paid')
                ->get();
            //Log::debug("Procesing CDRS");
            foreach ($cdrTotals as $cdrTotal) {
                $cdrTotalHistory = CallDetailRegisterTotalsHistory::where('call_detail_register_totals_id', $cdrTotal->id)->firstOrNew();

                try {
                    [$subTotal, $taxTotal, $totalWithTax, $globalTotalPaid, $taxes] = $this->getTotals($customer, $cdrTotal, $service);
                    //Log::debug("Totals GOT");
                } catch (Throwable $th) {
                    //Log::debug($th->getMessage());
                    if ($th instanceof PrepaidTotalCostCannotBeZero) {
                        //Costo nunca deberia ser zero, algun error raro estaria pasando;
                        Log::error($th->getMessage());

                        continue;
                    }
                    if ($customer->auto_replenish_amount > 0) {
                        $response = $this->checkLowBalance($customer);
                        if ($response === true) {
                            $customer->refresh();
                        }
                    }

                    continue;
                }

                //Log::debug("Updating Balance");
                //se actualiza el history y se crea nuevo CHI.
                DB::beginTransaction();

                try {

                    $cdrTotalHistory->amount = $totalWithTax;
                    $cdrTotalHistory->amountbruto = $cdrTotal->exclusive_cost;
                    $cdrTotalHistory->taxamount = $taxTotal;
                    //if new, se asigna a un cdr total.
                    $cdrTotalHistory->call_detail_register_totals_id = $cdrTotalHistory->call_detail_register_totals_id ?? $cdrTotal->id;
                    $cdrTotalHistory->save();
                    //Log::debug("Updated CDR History");

                    $chi = new CallHistoryIndi();
                    $chi->call_detail_register_totals_id = $cdrTotal->id;
                    $chi->amout = $totalWithTax;
                    $chi->amoutbruto = $subTotal;
                    $chi->taxamount = $taxTotal;
                    $chi->type = $service->taxType ? 1 : 2;

                    if ($chi->type == 1) {
                        $chi->tax_type_id = $service->tax_type_id;
                        $chi->percent = $service->taxType->percent;
                    }
                    $chi->save();
                    //Log::debug("Updated CallHistoryIndi");
                    //Log::debug("Updating Balance");
                    $cdrTotal->prepaid_check = 1;
                    $cdrTotal->exclusive_cost_paid = $globalTotalPaid;
                    $cdrTotal->save();
                    //Log::debug("Updated CallDetailRegisterTotal");

                    foreach ($taxes as $tax) {
                        $tax->pbxTaxTypesHistory()->create($this->generateHistoryTax($tax));
                    }
                    //Log::debug("Updated HistoryIndiTaxTypes");

                    $balanceCustomer->amount_op = $balanceCustomer->amount_op + $totalWithTax; // acumulado de lo cobrado
                    $balanceCustomer->amount_final = $customer->balance - $totalWithTax; // se resta el balance dle customer y el balance2 de lo cobrado
                    $balanceCustomer->payment_id = null;
                    $balanceCustomer->user_id = $customer->id;
                    $balanceCustomer->save();
                    //Log::debug("Updated Balance Customer");
                    //Log::debug("Previous Balance: {$customer->balance}");
                    //Log::debug("Charging Mont: {$totalWithTax}");
                    $customer->balance = $customer->balance - $totalWithTax;
                    //Log::debug("Current Balance: {$customer->balance}");
                    $customer->save();
                    //Log::debug("Updated Customer");
                } catch (Throwable $th) {
                    Log::error($th->getMessage());
                    DB::rollback();

                    return;
                }

                DB::commit();

                //If the customer don't have more money, just kick it outside (WIP)
                if ($customer->balance === 0) {
                    //Log::debug("Out you go");
                    break 2;
                }
            }

            //send mail when low fonds.
            if (! $customer->auto_debit && $customer->email_low_balance_notification >= $customer->balance) {
                if($customer->status_payment == "prepaid") {
                    LowBalanceEvent::dispatch($customer->id);
                } else {
                    Log::debug('suspendido por ser postpaid prepaid ');

                }

            }

            if ($customer->auto_debit === true && $customer->minimun_balance >= $customer->balance) {
                //A last recharge
                $this->checkLowBalance($customer);
            }

        }

    }

    /**
     * @throws Exception
     */
    public function rechargeUserCredit(User $customer): array
    {
        //copy and paste with some changes (early return).
        $config = CustomerConfig::where('customer_id', $customer->id)->where('enable_auto_debit', 1)->first();
        $paymentAccount = PaymentAccount::with('country', 'state')
            ->where('client_id', $customer->id)
            ->where('main_account', 1)
            ->where('status', 'A')->whereNull('deleted_at')->first();

        if ($paymentAccount == null) {
            //Log::debug('A Payment Account is required so the user credit can be recharged');
            return ['success' => false];
        }
        /* @var PaymentAccount $paymentAccount */
        if (is_null($config)) {
            //Log::debug('A Customer Configuration is required so the user credit can be recharged');
            return ['success' => false];
        }

        if (! $customer->auto_debit) {
            //Log::debug('The auto_replenish_amount must be higher than 0');
            return ['success' => false];
        }

        if ($customer->auto_replenish_amount === 0 or $customer->auto_replenish_amount < 0) {
            //Log::debug('The auto_replenish_amount must be higher than 0');
            return ['success' => false];
        }

        $service = new UserRechargeBalanceService($customer, new PaymentService($customer, $paymentAccount));
        $val = $service->handle($customer->auto_replenish_amount * 100);

        //Log::debug($val);
        if ($val) {
            $customer->refresh();

            return ['success' => true, 'balance_final' => $customer->balance];
        }

        return ['success' => false];
    }

    public function getTaxesTotal(PbxServices $service, $price): array
    {

        if ($service->pbxServiceTaxTypesCdrs->isNotEmpty()) {
            return $this->getTaxes($service->pbxServiceTaxTypesCdrs, $price);
        }

        if ($service->pbxServiceTaxTypes->isNotEmpty()) {
            return $this->getTaxes($service->pbxServiceTaxTypes, $price);
        }

        return [[], 0];
    }

    public function getTaxes(Collection $taxes, $price): array
    {
        $totalTax = 0;

        $taxes->each(function ($tax) use ($price, &$totalTax) {
            if ($tax->compound_tax == 0) {
                $tax->tax = $price * (($tax->percent ?? 0) / 100);
                $tax->price = $price;
                $totalTax += $tax->tax;
            }
        });

        $taxes->each(function ($tax) use ($price, &$totalTax) {
            if ($tax->compound_tax == 1) {
                $tax->price = ($price + $totalTax);
                $tax->tax = $tax->price * (($tax->percent ?? 0) / 100);
                $totalTax += $tax->tax;
            }
        });

        return [$taxes, $totalTax];

    }

    public function generateHistoryTax($tax): array
    {
        return [
            'percent' => $tax->percent,
            'compound_tax' => $tax->compound_tax,
            'amount' => $tax->price,
            'tax' => $tax->tax,
            'pbx_services_id' => $tax->pbx_services_id,
            'pbx_tax_types_cdr_id' => $tax->id,
        ];
    }
}
