<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRateGroup extends Model
{
    use HasFactory;

    protected $table = 'prefixrate_groups';

    protected $fillable = ['prefix', 'name', 'country_id', 'status','category', 'rate_per_minute','prefixrate_groups_id','company_id','creator_id'];
}
