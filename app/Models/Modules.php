<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [ 'name', 'description', 'version', 'image', 'status', 'slug', 'company_id', 'creator_id'];
}
