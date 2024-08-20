<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseTemplate extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_INACTIVE = 'Inactive';

    public const TERM_DAILY = 'daily';
    public const TERM_WEEKLY = 'weekly';
    public const TERM_MONTLY = 'monthly';
    public const TERM_BIMONTLY = 'bimonthly';
    public const TERM_QUARTERLY = 'quarterly';
    public const TERM_BIANNUAL = 'biannual';
    public const TERM_YEARLY = 'yearly';

    protected $fillable = [
        'payment_method_id',
        'items_id',
        'company_id',
        'amount',
        'providers_id',
        'expense_category_id',
        'notification',
        'days_after_payment_date',
        'initial_status',
        'term',
        'last_date',
        'expense_date',
        'description',
        'subject',
        'status',
        'template_expense_number',
        'customer_id',
        'name'
    ];

    public function companySettings(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);

    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('expense_category_id')) {
            $query->whereCategory($filters->get('expense_category_id'));
        }

        if ($filters->get('user_id')) {
            $query->whereUser($filters->get('user_id'));
        }


        if ($filters->get('providers_id')) {
            $query->WhereProvider($filters->get('providers_id'));
        }
        // if ($filters->get('expense_id')) {
        //     $query->whereExpense($filters->get('expense_id'));
        // }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->expensesBetween($start, $end);
        }

        if ($filters->get('template_expense_number')) {
            $query->expenseNumber($filters->get('template_expense_number'));
        }

        // if ($filters->get('customcode')) {
        //     $query->expenseCustomcode($filters->get('customcode'));
        // }

        if ($filters->get('providers_id')) {
            $query->WhereProvider($filters->get('providers_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'expense_date';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        // status
        // if ($filters->get('status') && $filters->get('status') != 'All') {
        //     $query->where('expenses.status', $filters->get('status'));
        // }
    }

    public function scopeWhereCategory($query, $categoryId)
    {
        return $query->where('expense_templates.expense_category_id', $categoryId);
    }

    public function scopeWhereExpense($query, $expense_id)
    {
        $query->orWhere('id', $expense_id);
    }

    public function scopeWhereProvider($query, $providers_id)
    {
        return $query->where('expense_templates.providers_id', $providers_id);
    }

    public function scopewhereUser($query, $user_id)
    {
        return $query->where('expense_templates.customer_id', $user_id);
    }

    public function scopeExpensesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'expense_templates.expense_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeExpenseNumber($query, $expenseNumber)
    {
        return $query->where('expense_templates.template_expense_number', 'LIKE', '%'.$expenseNumber.'%');
    }

    // public function scopeExpenseCustomcode($query, $customcode)
    // {
    //     // filtrar por customcode de cliente
    //     $query->whereHas('user', function ($query) use ($customcode) {
    //         $query->where('customcode', 'LIKE', '%' . $customcode . '%');
    //     });
    // }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('category', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%');
            })
                ->orWhere('notes', 'LIKE', '%'.$term.'%');
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('expense_templates.company_id', $company_id);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function getNextExpenseTemplateNumber($value)
    {

        // Get the last created order
        $expenseTemplate = ExpenseTemplate::where('template_expense_number', 'LIKE', $value.'-%')
            ->orderBy('created_at', 'desc')
            ->first();
        if (! $expenseTemplate) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $expenseTemplate->template_expense_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', intval($number) + 1);
    }

    public function getExpenseNumAttribute()
    {
        $position = $this->strposX($this->template_expense_number, "-", 1) + 1;

        return substr($this->template_expense_number, $position);
    }

    public function getExpensePrefixAttribute()
    {
        $prefix = explode("-", $this->template_expense_number)[0];

        return $prefix;
    }

    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
