<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceExtension extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'pbx_extension_id',
        'template_extension_id',
        'company_id',
        'creator_id',
        'pbx_extension_name',
        'pbx_extension_ext',
        'pbx_extension_email',
        'pbx_extension_ua_fullname',
        'template_extension_name',
        'template_extension_rate',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'ext', 'extensions_number');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class, 'ext', 'extensions_number');
    }
}
