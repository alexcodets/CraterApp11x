<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'only_cash',
        'add_payment_gateway',
        'paypal_button',
        'stripe_button',
        'account_accepted',
        'company_id',
        'payment_gateways_id',
        'for_customer_use',
        'generate_expense',
        'void_refund',
        'generate_expense_id',
        'void_refund_expense_id',
        'expense_import',
        'show_notes_table'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeWhereIsMultiple($query, $value)
    {
        $query->where('is_multiple', $value);
    }

    public function scopeWherePaymentMethod($query, $payment_id)
    {
        $query->orWhere('id', $payment_id);
    }

    public function scopeWhereSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('method_id')) {
            $query->wherePaymentMethod($filters->get('method_id'));
        }

        if ($filters->get('company_id')) {
            $query->whereCompany($filters->get('company_id'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'id';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public static function createPaymentMethod($request)
    {
        $data = $request->validated();
        $data['company_id'] = $request->header('company');

        \Log::debug($data);
        $paymentMethod = self::create($data);

        return $paymentMethod;
    }

    public function expCatGen(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'generate_expense_id');
    }

    public function expCatVoid(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'void_refund_expense_id');
    }
}
