<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxPackagesCdrStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'pbx_packages_id',
        'status',
    ];
}
