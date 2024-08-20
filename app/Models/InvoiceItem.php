<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'name',
        'item_id',
        'description',
        'company_id',
        'quantity',
        'price',
        'discount_type',
        'discount_val',
        'total',
        'tax',
        'discount',
        'retention_id',
        'retention_concept',
        'retention_percentage',
        'retention_amount',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'total' => 'integer',
            'discount' => 'float',
            'quantity' => 'float',
            'discount_val' => 'integer',
            'tax' => 'integer'
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function avalaraTaxes(): HasMany
    {
        return $this->hasMany(AvalaraTax::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        $query->whereHas('invoice', function ($query) use ($start, $end) {
            $query->whereBetween(
                'invoice_date',
                [$start->format('Y-m-d'), $end->format('Y-m-d')]
            );
        });
    }

    public function scopeInvoicesPaidStatus($query, $paid_status)
    {
        if (in_array("ALL", $paid_status)) {
            $query->whereHas('invoice', function ($query) use ($paid_status) {
                $query->where('paid_status', '!=', null);
            });
        } else {
            ;
            $query->whereHas('invoice', function ($query) use ($paid_status) {
                $query->whereIn('paid_status', $paid_status);
            });
        }
    }

    public function scopeInvoicesCustomer($query, $customers_id)
    {
        $query->whereHas('invoice', function ($query) use ($customers_id) {
            $query->whereIn('user_id', $customers_id);
        });

    }

    public function scopeInvoicesUser($query, $users_id)
    {
        $query->whereHas('invoice', function ($query) use ($users_id) {
            $query->whereIn('creator_id', $users_id);
        });

    }

    public function scopeApplyInvoiceFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->invoicesBetween($start, $end);
        }

        if ($filters->get('paid_status')) {
            $filters->put('paid_status', explode(',', $filters->get('paid_status')));
            $query->InvoicesPaidStatus($filters->get('paid_status'));
        }

        if ($filters->get('customers_id')) {
            $filters->put('customers_id', explode(',', $filters->get('customers_id')));
            $query->InvoicesCustomer($filters->get('customers_id'));
        }

        if ($filters->get('users_id')) {
            $filters->put('users_id', explode(',', $filters->get('users_id')));
            $query->InvoicesUser($filters->get('users_id'));
        }
    }

    public function scopeItemAttributes($query)
    {
        $query->select(
            DB::raw('sum(quantity) as total_quantity, sum(total) as total_amount, invoice_items.name')
        )->groupBy('invoice_items.name');
    }
}
