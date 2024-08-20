<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class customAppRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'office',
        'office_price',
        'bussiness',
        'bussiness_price',
        'web',
        'web_price',
        'agent',
        'agent_price',
        'supervisor',
        'supervisor_price',
        'mobile',
        'mobile_price',
        'crm',
        'crm_price',
        'call_pop_up',
        'call_pop_up_price'
    ];

    public function packages(): HasMany
    {
        // uno a muchos con pbx_packages y custom_app_rate_id
        return $this->hasMany(PbxPackages::class, 'custom_app_rate_id', 'id');
    }
}
