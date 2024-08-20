<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'package_group';

    protected $fillable = ['package_groups_id', 'packages_id'];
}
