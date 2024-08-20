<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLateFee extends Model
{
    use HasFactory;
    public const TYPE_PERCENTAGE = 0;
    public const TYPE_FIXED = 1;

    protected $fillable =
        [
            'amount',
            'type',
            'notice',
            'subtotal',
            'tax_amount',
            'total',
            'invoice_id',
            'value'
        ];

    public function getTypeNameAttribute(): string
    {
        if ($this->type === 0) {
            return 'Percentage';
        }
        if ($this->type === 1) {
            return 'Fixed';
        }

        return 'Not valid';
    }
}
