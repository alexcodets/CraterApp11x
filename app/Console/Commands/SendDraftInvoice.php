<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\ScheduleLog;
use Illuminate\Console\Command;
use Log;
use Throwable;

class SendDraftInvoice extends Command
{
    public const ALLOW_SEND_INVOICE_JOB = 'allow_send_invoice_job';
    public const SEND_EMAIL_DEACTIVE = 'send_email_deactive';
    public const PERIOD_TIME_RUN_SEND_INVOICE_JOB = 'period_time_run_send_invoice_job';
    public const TIME_INVOICES_DRAFT_SENT = 'time_invoices_draft_sent';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:send:draft';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send invoices with status draft';

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
        Log::debug('< Invoice Send Draft >');

        $companies = Company::wherehas('invoices', function ($query) {
            $query->whereStatus(Invoice::STATUS_DRAFT);
        })->with([
            'invoices' => function ($query) {
                $query->whereStatus(Invoice::STATUS_DRAFT);
            },
            'settings' => function ($query) {
                $query->whereIn('option', [
                    self::ALLOW_SEND_INVOICE_JOB,
                    self::PERIOD_TIME_RUN_SEND_INVOICE_JOB,
                    self::TIME_INVOICES_DRAFT_SENT,
                    self::SEND_EMAIL_DEACTIVE,
                    'invoice_mail_body',
                    'invoice_mail_subject'
                ]);
            }
        ])->get();

        foreach ($companies as $company) {
            $settings = $company->keySettings;

            if (isset($settings[self::TIME_INVOICES_DRAFT_SENT])) {
                $diff_in_minutes = Carbon::now()->diffInMinutes($settings[self::TIME_INVOICES_DRAFT_SENT]);
                if($diff_in_minutes < $settings[self::PERIOD_TIME_RUN_SEND_INVOICE_JOB]) {
                    Log::debug('!!! Diff in minutes is less than PERIOD_TIME_RUN_SEND_INVOICE_JOB !!!');

                    continue;
                }
            }

            Invoice::whereIn('id', $company->invoices->pluck('id')->toArray())->update(
                ['status' => Invoice::STATUS_SENT]
            );

            if (! isset($settings[self::ALLOW_SEND_INVOICE_JOB]) || ! $settings[self::ALLOW_SEND_INVOICE_JOB]) {
                Log::debug('!!! No allowed to send invoice job !!!');

                continue;
            }

            if (! isset($settings[self::SEND_EMAIL_DEACTIVE]) || $settings[self::SEND_EMAIL_DEACTIVE] != 'NO') {
                Log::debug('!!! No allowed to send invoice job !!!');

                continue;
            }

            Log::debug('< Send Invoices >');
            $this->sendInvoices($company);
            CompanySetting::setSettings([self::TIME_INVOICES_DRAFT_SENT => now()->format('Y-m-d H:i')], $company->id);
        }

        return self::SUCCESS;
    }

    public function sendInvoices(Company $company)
    {

        $data = [
            'from' => config('mail.from.address'),
            'to' => null,
            'subject' => $company->keySettings['invoice_mail_subject'] ?? 'New Invoice',
            'body' => $company->keySettings['invoice_mail_body'] ?? ''
        ];

        foreach ($company->invoices as $invoice) {
            try {
                $data['to'] = $invoice->user->email;
                /* @var Invoice $invoice */
                $invoice->send($data);
            } catch (Throwable $th) {
                //Log::debug($th->getMessage());
                $invoice->scheduleLogs()->create([
                    'module_name' => 'invoice:send:draft',
                    'message' => 'Error sending mail',
                    'extra_data' => json_encode([
                        'error' => $th->getMessage()
                    ]),
                    'lvl' => ScheduleLog::LVL_ERROR,
                ]);
            }
        }


    }
}
