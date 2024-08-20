<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvalaraTaxCagetory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'avalara_taxe_categories';

    public function taxes(): HasMany
    {
        return $this->hasMany(AvalaraTax::class, 'avalara_category_id');
    }
}
