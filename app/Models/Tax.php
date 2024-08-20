<?php

namespace Crater\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_type_id',
        'invoice_id',
        'estimate_id',
        'invoice_item_id',
        'item_id',
        'company_id',
        'name',
        'amount',
        'percent',
        'compound_tax',
        'pbx_package_id',
        'pbx_package_item_id',
        'pbx_service_item_id',
        'package_id',
        'package_item_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'percent' => 'float',
        ];
    }

    public function taxType(): BelongsTo
    {
        return $this->belongsTo(TaxType::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function estimateItem(): BelongsTo
    {
        return $this->belongsTo(EstimateItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public function scopeTaxAttributes($query)
    {
        $query->select(
            DB::raw('sum(amount) as total_tax_amount, tax_type_id')
        )->groupBy('tax_type_id');
    }

    public function scopeInvoicesBetween($query, $start, $end, $customer, $item, $item_id)
    {
        if ($item == 0) {
            if ($customer == 0) {
                $query->whereHas('invoice', function ($query) use ($start, $end) {
                    $query->where('paid_status', Invoice::STATUS_PAID)
                        ->whereBetween(
                            'invoice_date',
                            [$start->format('Y-m-d'), $end->format('Y-m-d')]
                        );
                })
                    ->orWhereHas('invoiceItem.invoice', function ($query) use ($start, $end) {
                        $query->where('paid_status', Invoice::STATUS_PAID)
                            ->whereBetween(
                                'invoice_date',
                                [$start->format('Y-m-d'), $end->format('Y-m-d')]
                            );
                    });
            } else {
                $query->whereHas('invoice', function ($query) use ($start, $end, $customer) {
                    $query->where('paid_status', Invoice::STATUS_PAID)
                        ->where('user_id', $customer)
                        ->whereBetween(
                            'invoice_date',
                            [$start->format('Y-m-d'), $end->format('Y-m-d')]
                        );
                })
                    ->orWhereHas('invoiceItem.invoice', function ($query) use ($start, $end, $customer) {
                        $query->where('paid_status', Invoice::STATUS_PAID)
                            ->where('user_id', $customer)
                            ->whereBetween(
                                'invoice_date',
                                [$start->format('Y-m-d'), $end->format('Y-m-d')]
                            );
                    });

            }

        } else {

            $query->whereHas('invoiceItem.invoice', function ($query) use ($start, $end) {
                $query->where('paid_status', Invoice::STATUS_PAID)
                    ->whereBetween(
                        'invoice_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    );
            });

        }
    }

    public function scopeWhereInvoicesFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));

            $query->invoicesBetween($start, $end, $filters->get('customer'), $filters->get('item'), $filters->get('item_id'));
        }
    }
}
