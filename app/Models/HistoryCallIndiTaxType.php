<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistoryCallIndiTaxType extends Model
{
    use HasFactory;

    protected $fillable = [
        'pbx_services_id',
        'tax_types_id',
        'name',
        'percent',
        'amount',
        'tax',
        'compound_tax',
        'status',
    ];

    /**
     * Get the parent commentable model (post or video).
     */
    public function taxable(): MorphTo
    {
        return $this->morphTo();
    }
}
