<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvalaraTaxType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'avalara_taxe_types';

    public function taxes(): HasMany
    {
        return $this->hasMany(AvalaraTax::class, 'avalara_type_id');
    }
}
