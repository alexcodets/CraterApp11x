<?php

namespace Crater\Console\Commands;

use Crater\Mail\ExpensePendingMail;
use Crater\Models\Expense;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExpensePendingNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:pending-notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for each pending notification that have enabled the option';

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

        $expenses = Expense::query()
            ->where('expense_date', '<=', now()->format('Y-m-d'))
            ->where('notification', '=', true)
            ->whereHas('companySettings', function (Builder $query) {
                $query->where('option', 'job_expense_pending_enable')->where('value', 1);
            })->whereHas('companySettings', function (Builder $query) use ($time) {
                $query->where('option', '=', 'job_expense_pending_time_run')->where('value', '=', $time);
            })->with([
                'company',
                'company.settings' => function ($query) use ($time) {
                    $query->where('option', 'job_expense_pending_mail_body')
                        ->Orwhere('option', 'job_expense_pending_mail_subject')
                        ->Orwhere('option', 'job_expense_pending_mail_bbc');
                },
                'company.users' => function ($query) use ($time) {
                    $query->where('role', 'super admin')->where('email_expenses', 1);
                }
            ])->get();

        if ($expenses->isEmpty()) {
            Log::debug('is Empty');
        }

        foreach ($expenses as $expense) {
            /* @var Expense $expense */

            $settings = $expense->company->setting;

            foreach ($expense->company->users as $user) {

                try {
                    Mail::to($user->email)->send(new ExpensePendingMail($expense));

                } catch (Throwable $th) {
                    Log::debug('Error sending mail for ExpensePendingNotify');
                    Log::debug($th->getMessage());
                }
            }

            if ($settings['job_expense_pending_mail_bbc']) {
                Mail::to($settings['job_expense_pending_mail_bbc'])->send(new ExpensePendingMail($expense));
            }

        }

        return self::SUCCESS;
    }
}
