<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class pbxExtensionCustomSearch extends Model
{
    use HasFactory;

    // relation with department
    public function department(): BelongsTo
    {
        // uno a uno con CustomSearch
        return $this->belongsTo(CustomSearch::class, 'custom_search_id');
    }
}
