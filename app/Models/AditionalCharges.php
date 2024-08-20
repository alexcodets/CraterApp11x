<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AditionalCharges extends Model
{
    use HasFactory;
    use SoftDeletes;

    // nombre de la tabla
    protected $table = 'aditional_charges';

    protected $fillable = [
        'company_id',
        'creator_id',
        'profile_extension_id',
        'profile_did_id',
        'description',
        'amount',
        'status',
    ];

    // para el softDelete
    public function profileExtension(): BelongsTo
    {
        return $this->belongsTo(ProfileExtensions::class);
    }
}
