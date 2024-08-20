<?php

namespace Crater\Models;

use Crater\CorePos\Models\PosCashRegister;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashHistory extends Model
{
    use HasFactory;

    protected $appends = [
        'formattedSelectReport'
    ];

    public function posCashRegister(): BelongsTo
    {
        return $this->belongsTo(PosCashRegister::class);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'estimate_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

        return $query;
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function getFormattedSelectReportAttribute($value)
    {
        $openDate = $this->open_date != null ? ' / '.$this->open_date : '';
        $closeDate = $this->close_date != null ? ' / '.$this->close_date : '';
        $reference = $this->ref != null ? $this->ref : '';
        $userName = $this->user_name != null ? ' / '.$this->user_name : '';
        $result = $reference.$userName.$openDate.$closeDate;

        return $result;
    }
}
