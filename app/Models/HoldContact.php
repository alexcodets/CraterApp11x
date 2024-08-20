<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoldContact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'last_name',
        'identification',
        'phone',
        'second_phone',
        'email',
        'hold_invoice_id',
    ];

    public function holdInvoice(): BelongsTo
    {
        return $this->belongsTo(HoldInvoice::class);
    }
}
