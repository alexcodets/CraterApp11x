<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeSetting extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'public_key',
        'private_key',
        'secret_key',
        'status',
        'currency',
        'environment',
        'creator_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
