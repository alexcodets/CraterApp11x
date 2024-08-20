<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tax_group';

    protected $fillable = ['tax_groups_id', 'taxes_id'];
}
