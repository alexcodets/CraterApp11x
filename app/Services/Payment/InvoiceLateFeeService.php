<?php

namespace Crater\Services\Payment;

use Crater\DataObject\LateFeeDO;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\InvoiceLateFee;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Log;
use Throwable;

class InvoiceLateFeeService
{
    public function execute()
    {
        Log::debug('-----Executing Invoice Late Fee Service-----');
        $time = now()->format('H:i');
        Log::debug('Time');
        Log::debug($time);

        /* @var Collection $companies */

        //TODO: Cambiar Not null for value of time now.
        $companies = Company::whereHas('settings', function ($query) use ($time) {
            $query
                ->where('option', 'late_fee_hour')
                ->where('value', $time);
            // ->whereNotNull('value');
        })->with('settings', function ($query) {
            $query->where('option', 'like', 'invoice_late%')
                ->orWhere('option', 'late_fee_hour');
        })->get();

        if ($companies->isEmpty()) {
            Log::debug('There was not match Company');

            return 0;
        }

        /*  $companies = $companies->map(function ($company) {
            $company->setting = collect();
            $company->setting = $company->settings->flatMap(function ($setting) {
                return [$setting->option => $setting->value];
            });
            return $company;
        });*/

        $companiesId = $companies->pluck('id');

        Log::debug('company');
        Log::debug($companiesId);


        $invoices = Invoice::where('paid_status', '!=', Invoice::STATUS_PAID)->whereIn('company_id', $companiesId)
            ->whereNotIn('status', [
                Invoice::STATUS_COMPLETED, Invoice::STATUS_DRAFT
            ])
            ->where('due_date', '<=', now())
            ->selectRaw('*, DATEDIFF(CURDATE(), due_date) as days_overdue')->get();

        if ($invoices->isEmpty()) {
            Log::debug('There was not match Invoice');

            return 0;
        }

        foreach ($invoices as $invoice) {
            try {
                /* @var Company $company */
                $company = $companies->firstWhere('id', $invoice->company_id);
                $invoceLateFeeRetroactive = CompanySetting::where('option', 'invoice_late_fee_retroactive')->where('company_id', $company->id)->pluck('value');

                if (is_null($company ?? $company->setting)) {
                    Log::debug("There was not setting for the company id:{$company->id}");

                    continue;
                }
                Log::debug("------Company: {$company->id} / Invoice {$invoice->id}------");
                Log::debug(" Invoice {$invoice->days_overdue}------");
                $lateFee = $this->extracted($company, $invoice, $invoceLateFeeRetroactive);

                if (is_null($lateFee)) {
                    Log::debug("There was not late fee for the invoice: {$invoice->id} from the company id:{$company->id}");

                    continue;
                }

                if (! $lateFee->isNew()) {
                    Log::debug("That Fee has been already applied to the invoice: {$invoice->id}");

                    continue;
                }

                $this->process($invoice, $lateFee);
            } catch (Throwable $th) {
                Log::debug("Error with the Invoice: {$invoice->id} from the company id: {$company->id}");
                Log::debug($th->getMessage());
                //throw $th;
            }

            try {
                $this->mailing($invoice, $company);
            } catch (\Throwable $th) {
                Log::debug("Error while sending mail for invoice {$invoice->id}");
                //throw $th;
            }
        }
        //Log::debug('----Process Finished---');

        return $invoices;
        //return $companies;
    }

    public function mailing(Invoice $invoice, Company $company)
    {
        $data = [
            'from' => config('mail.from.address'),
            'to' => $invoice->user->email,
            'subject' => $company->keySettings['invoice_mail_subject'] ?? 'New Invoice',
            'body' => $company->keySettings['invoice_mail_body'] ?? ''
        ];

        $invoice->send($data);
    }

    public function process(Invoice $invoice, LateFeeDO $lateFeeDo)
    {

        try {

            $lateFeeDo->calculateTotals();
            DB::beginTransaction();

            try {

                $invoice->due_amount += $lateFeeDo->total;
                $invoice->total += $lateFeeDo->total;
                $invoice->late_fee_amount += $lateFeeDo->fee;
                $invoice->late_fee_taxes += $lateFeeDo->tax;
                $invoice->save();
            } catch (ValidationException $e) {
                DB::rollback();

                //Log::debug($e->getMessage());
                //Log::debug("Error with validation while saving data for Invoice: {$invoice->id}");
                throw new Exception("Error with validation while saving data for Invoice: {$invoice->id}");
            } catch (Exception $e) {
                DB::rollback();

                //Log::debug($e->getMessage());
                throw new Exception("Error while saving data for Invoice: {$invoice->id}");
            }

            try {
                InvoiceLateFee::create([
                    'amount' => $lateFeeDo->amount,
                    'value' => $lateFeeDo->amount,
                    'type' => $lateFeeDo->getType(),
                    'notice' => $lateFeeDo->name,
                    'subtotal' => $lateFeeDo->fee,
                    'tax_amount' => $lateFeeDo->tax,
                    'total' => $lateFeeDo->total,
                    'invoice_id' => $invoice->id,
                ]);
            } catch (ValidationException $e) {
                DB::rollback();

                //Log::debug($e->getMessage());
                throw new Exception("Error with validation while saving data for InvoiceLateFee from Invoice: {$invoice->id}");
            } catch (Exception $e) {
                DB::rollback();

                //Log::debug($e->getMessage());
                throw new Exception("Error while saving data for Invoice: {$invoice->id}");
            }

            DB::commit();
        } catch (Throwable $th) {
            Log::debug('Bug inside procesing');
            Log::debug($th->getMessage());
            //throw $th;
        }
    }

    /**
     * @param Company $company
     * @param Invoice $invoice
     * @return LateFeeDO|null
     * @throws Exception
     * @noinspection PhpUndefinedFieldInspection
     */
    public function extracted(Company $company, Invoice $invoice, $lateFeeRetroactive): ?LateFeeDO
    {
        // crear migraciaon para agregar el invoice late fee retraoacive false
        // false validacion debe ser == aplica forma normal
        // true validacion debe ser <= para aplicar a forma retroactiva
        //  $invoceLateFeeRetroactive =  CompanySetting::where('option','invoice_late_fee_retroactive')->pluck('value');
        // Log::debug('invoice late fee retro' , ['test' => $lateFeeRetroactive->first()]);

        if ($lateFeeRetroactive->first() == '1') {

            Log::debug('invoice late fee retro if ');
            if (($company->setting['invoice_late_fee_active_one'] ?? null) && ($company->setting['invoice_late_fee_days_one'] <= $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_one', $invoice))) {
                Log::debug('Inside Day 1');
                if (is_null($company->setting['invoice_late_fee_type_one_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day One');
                }
                if (is_null($company->setting['invoice_late_fee_type_one'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day One');
                }

                return $this->getLateFee('invoice_late_fee_one', $invoice, $company->setting['invoice_late_fee_type_one'], $company->setting['invoice_late_fee_type_one_value']);
            }

            if (($company->setting['invoice_late_fee_active_two'] ?? null) && ($company->setting['invoice_late_fee_days_two'] <= $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_two', $invoice))) {
                Log::debug('Inside Day 2');

                if (is_null($company->setting['invoice_late_fee_type_two_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day Two');
                }
                if (is_null($company->setting['invoice_late_fee_type_two'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day Two');
                }

                return $this->getLateFee('invoice_late_fee_two', $invoice, $company->setting['invoice_late_fee_type_two'], $company->setting['invoice_late_fee_type_two_value']);
            }

            if (($company->setting['invoice_late_fee_active_three'] ?? null) && ($company->setting['invoice_late_fee_days_three'] <= $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_three', $invoice))) {
                Log::debug('Inside Day 3');
                if (is_null($company->setting['invoice_late_fee_type_three_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day Three');
                }
                if (is_null($company->setting['invoice_late_fee_type_three'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day Three');
                }

                return $this->getLateFee('invoice_late_fee_three', $invoice, $company->setting['invoice_late_fee_type_three'], $company->setting['invoice_late_fee_type_three_value']);
            }

            return null;

        } else {


            if (($company->setting['invoice_late_fee_active_one'] ?? null) && ($company->setting['invoice_late_fee_days_one'] == $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_one', $invoice))) {
                Log::debug('Inside Day 1');
                if (is_null($company->setting['invoice_late_fee_type_one_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day One');
                }
                if (is_null($company->setting['invoice_late_fee_type_one'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day One');
                }

                return $this->getLateFee('invoice_late_fee_one', $invoice, $company->setting['invoice_late_fee_type_one'], $company->setting['invoice_late_fee_type_one_value']);
            }

            if (($company->setting['invoice_late_fee_active_two'] ?? null) && ($company->setting['invoice_late_fee_days_two'] == $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_two', $invoice))) {
                Log::debug('Inside Day 2');

                if (is_null($company->setting['invoice_late_fee_type_two_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day Two');
                }
                if (is_null($company->setting['invoice_late_fee_type_two'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day Two');
                }

                return $this->getLateFee('invoice_late_fee_two', $invoice, $company->setting['invoice_late_fee_type_two'], $company->setting['invoice_late_fee_type_two_value']);
            }

            if (($company->setting['invoice_late_fee_active_three'] ?? null) && ($company->setting['invoice_late_fee_days_three'] == $invoice->days_overdue && $this->noYetApplied('invoice_late_fee_three', $invoice))) {
                Log::debug('Inside Day 3');
                if (is_null($company->setting['invoice_late_fee_type_three_value'] ?? null)) {
                    throw new Exception('There was not a value set for Late fee Day Three');
                }
                if (is_null($company->setting['invoice_late_fee_type_three'] ?? null)) {
                    throw new Exception('There was not a type for Late fee Day Three');
                }

                return $this->getLateFee('invoice_late_fee_three', $invoice, $company->setting['invoice_late_fee_type_three'], $company->setting['invoice_late_fee_type_three_value']);
            }

            return null;
        }

        // return null;
    }

    /**
     * @throws Exception
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getLateFee(string $name, Invoice $invoice, string $type, $amount): LateFeeDO
    {
        if (InvoiceLateFee::where('notice', $name)->where('invoice_id', $invoice->id)->exists()) {
            throw new Exception("The late fee {$name} for invoice {$invoice->id} already exists");
        }

        //return new LateFeeDO($name, $invoice->days_overdue, $type, $amount);
        return new LateFeeDO($name, $invoice, $type, $amount);
    }

    public function noYetApplied(string $name, Invoice $invoice): bool
    {
        $existsLateFee = InvoiceLateFee::where('notice', $name)->where('invoice_id', $invoice->id)->doesntExist();
        Log::debug('no yet applied', ['exists' => $existsLateFee]);

        return  $existsLateFee;
    }
}
