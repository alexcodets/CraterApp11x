<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoldTable extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'table_id',
        'hold_invoice_id',
        'quantity'
    ];

    /**
     *
     *
     * @var string
     */
    protected $table = 'hold_tables';

    public function holdInvoice(): HasMany
    {
        return $this->hasMany(Holdinvoice::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
