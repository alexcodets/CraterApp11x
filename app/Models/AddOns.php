<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOns extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'description', 'version', 'image', 'status', 'slug', 'company_id', 'creator_id'];
}
