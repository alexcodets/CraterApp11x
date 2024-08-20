<?php

namespace Crater\CorePos\Models;

use Crater\Models\User;
use Crater\Traits\ModelPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelPagination;

    public function cashRegister(): BelongsToMany
    {
        return $this->belongsToMany(PosCashRegister::class, 'cash_register_table_table_pivot', 'table_id', 'cash_register_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }
}
