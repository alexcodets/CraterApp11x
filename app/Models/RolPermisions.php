<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermisions extends Model
{
    use HasFactory;

    protected $table = 'rol_permisions';

    protected $fillable = [
        'rol_id',
        'module',
        'access',
        'create',
        'read',
        'update',
        'delete',
        'company_id',
        'creator_id',
    ];
}
