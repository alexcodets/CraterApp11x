<?php

namespace Crater\Console\Commands;

use Crater\Models\Expense;
use Crater\Models\ExpenseTemplate;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

class ExpenseTemplateGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:template-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::debug('Expense Template Generate');
        Log::debug(now()->format('H:i'));

        $templates = $this->getTemplates();

        Log::debug('Templates');
        Log::debug($templates);

        if ($templates->isNotEmpty()) {
            Log::debug('<------ Expense Template Generate Start -------->');
        }

        foreach ($templates as $template) {
            /* @var ExpenseTemplate $template */
            Log::debug('Template ', $template->toArray());

            try {
                Cache::lock('template_prefix', 10)->block(12, function () use ($template) {

                    $prefix = $template->companySettings()->where(['option' => 'expense_prefix'])->first()->value ?? null;
                    DB::transaction(function () use ($template, $prefix) {
                        $expense = Expense::create([
                            'subject' => $template->subject,
                            'notes' => $template->description,
                            'payment_method_id' => $template->payment_method_id,
                            'items_id' => $template->items_id,
                            'user_id' => $template->customer_id ?? $template->user_id,
                            'company_id' => $template->company_id,
                            'amount' => $template->amount,
                            'providers_id' => $template->providers_id,
                            'expense_category_id' => $template->expense_category_id,
                            'notification' => $template->notification,
                            'status' => $template->initial_status,
                            'expense_number' => "{$prefix}-".Expense::getNextExpenseNumber($prefix),
                            'payment_date' => now(),
                            'expense_date' => $template->initial_status == Expense::STATUS_PENDING
                                ? now()->addDays($template->days_after_payment_date ?? 0)->format('Y-m-d')
                                : now()->format('Y-m-d'),
                        ]);

                        $template->last_date = now()->format('Y-m-d');
                        $template->save();

                        Log::debug("Expense ID: {$expense->id}");
                    });
                });

            } catch (Throwable $th) {
                Log::error('A error while generating expense for template: '.$template->id);
                Log::error($th->getTraceAsString());
            }

        }

        if ($templates->isNotEmpty()) {
            Log::debug('</----- Expense Template Generate End ------->');
        }

        return 0;
    }

    public function getTemplates(): Collection
    {
        return ExpenseTemplate::query()
            ->where('status', ExpenseTemplate::STATUS_ACTIVE)
            ->where(function (builder $query) {
                $query->where(function (builder $query) {
                    $query->whereNull('last_date');
                    $query->where(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_DAILY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 1 DAY)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_WEEKLY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 1 WEEK)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_MONTLY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 1 MONTH)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_BIMONTLY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 2 MONTH)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_QUARTERLY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 3 MONTH)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_BIANNUAL);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 6 MONTH)');
                    });
                    $query->orWhere(function (builder $query) {
                        $query->where('term', '=', ExpenseTemplate::TERM_YEARLY);
                        $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 1 YEAR)');
                    });
                })->orWhere(function (builder $query) {
                    $query->where(function (builder $query) {
                        $query->whereNotNull('last_date');
                        $query->where(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_DAILY);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 1 DAY)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_WEEKLY);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 1 WEEK)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_MONTLY);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 1 MONTH)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_BIMONTLY);
                            $query->whereRaw('CURDATE() = DATE_ADD(expense_date, INTERVAL 2 MONTH)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_QUARTERLY);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 3 MONTH)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_BIANNUAL);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 6 MONTH)');
                        });
                        $query->orWhere(function (builder $query) {
                            $query->where('term', '=', ExpenseTemplate::TERM_YEARLY);
                            $query->whereRaw('CURDATE() = DATE_ADD(last_date, INTERVAL 1 YEAR)');
                        });
                    });
                });
            })
            ->whereHas('companySettings', function (Builder $query) {
                $query->where('option', 'job_expense_template_enable')->where('value', 1);
            })
            ->whereHas('companySettings', function (Builder $query) {
                $query->where('option', 'job_expense_template_time_run')->where('value', now()->format('H:i'));
            })->with('company')->get();
    }
}
