<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxGroupType extends Model
{
    use HasFactory;

    protected $fillable = ['tax_groups_id', 'tax_types_id', 'status'];
}
