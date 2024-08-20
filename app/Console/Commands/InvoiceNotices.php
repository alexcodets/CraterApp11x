<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Log;

class InvoiceNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoices notices';

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
     */
    public function handle(): int
    {

        $time = now()->format('H:i');

        $customers = User::query()
            ->whereHas('customerConfig', function (Builder $query) {
                $query->where('enable_auto_debit', 1);
            })->whereHas('companySettings', function (Builder $query) {
                $query->where('option', 'allow_reminder_payment_job')->where('value', 1);
            })->whereHas('companySettings', function (Builder $query) use ($time) {
                $query->where('option', 'time_run_reminder_payment_job')->where('value', $time);
            })->whereHas('billingAddress', function (Builder $query) {
                $query->where('payment_notices', '=', 1);
            })->whereHas('invoices', function (Builder $query) {
                $query->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_COMPLETED])
                    ->where('paid_status', '!=', Invoice::STATUS_PAID);
            })->with('invoices', function ($query) {
                $query->whereNotIn('status', [Invoice::STATUS_DRAFT, Invoice::STATUS_COMPLETED])
                    ->where('paid_status', '!=', 'PAID')
                    ->orderBy('due_date', 'asc');
            })->with('company.settings', function ($query) {
                $query->whereIn('option', [
                    'invoice_notices_settings_notice_1',
                    'invoice_notices_settings_notice_2',
                    'invoice_notices_settings_notice_3',
                    'invoice_notices_settings_notice_1_type',
                    'invoice_notices_settings_notice_2_type',
                    'invoice_notices_settings_notice_3_type',
                ]);
            })->customer()->customerActive()->get();

        /* @var Collection $customers */
        foreach ($customers as $customer) {
            /* @var User $customer */
            Log::debug("Customer: {$customer->id}");
            $settings = $customer->company->setting;
            for ($i = 3; $i > 0; $i--) {

                if (is_null($settings["invoice_notices_settings_notice_{$i}"] ?? null) || is_null($settings["invoice_notices_settings_notice_{$i}_type"] ?? null)) {
                    continue;
                }
                $this->process($customer, $i, $settings);
            }

            self::noticesPending($customer->company_id, $customer->id);
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    public function getInvoices(User $customer, int $attempts): Collection
    {
        return Invoice::where('company_id', $customer->company_id)->where('status', '!=', 'DRAFT')
            ->where('status', '!=', 'SAVE_DRAFT')
            ->where('status', '!=', 'COMPLETED')->where('paid_status', '!=', 'PAID')
            ->where('attempts', $attempts - 1)->whereNull('deleted_at')->where('user_id', $customer->id)->get();

    }

    public function process(User $customer, int $number, \Illuminate\Support\Collection $baseSettings)
    {
        $due_date = now()->format('Y-m-d');
        $invoices = $this->getInvoices($customer, $number);
        if ($invoices->isEmpty()) {
            Log::debug('No invoice');

            return;
        }

        $numToLetter = [
            1 => 'one',
            2 => 'two',
            3 => 'three'
        ];

        $settings = CompanySetting::whereCompany($customer->company_id)
            ->whereIn('option', ["invoice_notice_{$numToLetter[$number]}", "invoice_notice_{$numToLetter[$number]}_subject"])
            ->pluck('value', 'option')->toArray();

        if (count($settings) == 0) {
            Log::debug("Settings no found for $number");

            return;
        }

        $invoice_mail_body = $settings["invoice_notice_{$numToLetter[$number]}"];
        $invoice_mail_subject = $settings["invoice_notice_{$numToLetter[$number]}_subject"];

        foreach ($invoices as $invoice) {
            /* @var Invoice $invoice */
            Log::debug("Processing Invoice: {$invoice->id}");
            $due_datetest = '';

            $typeTime = $baseSettings["invoice_notices_settings_notice_{$number}_type"];
            $days = $baseSettings["invoice_notices_settings_notice_{$number}"];

            Log::debug($typeTime);

            if ($typeTime == 'After') {
                $due_datetest = Carbon::parse($invoice->due_date)->addDays($days)->format('Y-m-d');
            } else {
                $due_datetest = Carbon::parse($invoice->due_date)->subDays($days)->format('Y-m-d');
            }
            Log::debug("TestDate: {$due_datetest}, RealDate: {$due_date}");

            if ($due_datetest != $due_date) {
                continue;
            }
            if ($invoice->attempts <= 0 && $invoice->attempts >= 3) {
                continue;
            }

            $data = [
                'from' => config('mail.from.address'),
                'to' => $invoice->user->email,
                'subject' => $invoice_mail_subject,
                'body' => $invoice_mail_body,
            ];

            $invoice->sendInvoiceNotice($data);
            $invoice->autodebit_notices_check = 1;
            $invoice->attempts = $invoice->attempts + 1;
            $invoice->noeditable = 1;
            $invoice->save();

        }
    }

    public static function noticesPending(int $company, int $customer)
    {

        $daysob = CompanySetting::where('company_id', $company)->where('option', 'invoice_notices_settings_auto_debit_pending')->first();

        if (is_null($daysob)) {
            return;
        }
        $due_date = Carbon::now()->addDays($daysob->value)->format('Y-m-d');
        $customerid = CustomerConfig::where('enable_auto_debit', 1)->where('customer_id', '!=', 0)->where('company_id', $company)->pluck('customer_id');
        $customerid = User::whereIN('id', $customerid)->where('status_payment', 'prepaid')->pluck('id');

        $listinvoices = Invoice::whereIN('user_id', $customerid)->where('status', '!=', 'DRAFT')->where('status', '!=', 'SAVE_DRAFT')->where('status', '!=', 'COMPLETED')->where('paid_status', '!=', 'PAID')->where('autodebit_notices_check', 0)->where('due_date', $due_date)->whereIN('user_id', $customer)->whereNull('deleted_at')->get();

        $invoice_mail_body = CompanySetting::whereCompany($company)
            ->where('option', 'invoice_notice_unpaid')
            ->first();
        $invoice_mail_subject = CompanySetting::whereCompany($company)
            ->where('option', 'invoice_notice_unpaid_subject')
            ->first();

        foreach ($listinvoices as $invoice) {
            $data = [
                'from' => config('mail.from.address'),
                'to' => $invoice->user->email,
                'subject' => $invoice_mail_subject ? $invoice_mail_subject->value : 'Invoice Auto Debit notice',
                'body' => $invoice_mail_body ? $invoice_mail_body->value : '',
            ];

            $invoice->sendInvoiceNotice($data);
            $invoice->autodebit_notices_check = 1;
            $invoice->save();

        }

    }
}
