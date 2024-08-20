<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoldItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'unit_name',
        'discount',
        'discount_val',
        'tax',
        'total',
        'retentions_id',
        'retention_concept',
        'retention_percentage',
        'retention_amount',
        'hold_invoice_id',
        'item_id'
    ];

    public function holdInvoice(): BelongsTo
    {
        return $this->belongsTo(HoldInvoice::class);
    }
}
