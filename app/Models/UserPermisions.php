<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermisions extends Model
{
    use HasFactory;

    protected $table = 'user_permisions';

    // fillable
    protected $fillable = [
        'user_id',
        'module',
        'access',
        'create',
        'read',
        'update',
        'delete',
        'company_id',
        'creator_id'
    ];
}
