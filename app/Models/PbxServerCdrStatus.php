<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxServerCdrStatus extends Model
{
    use HasFactory;

    protected $fillable = ['pbx_servers_id', 'status'];
}
