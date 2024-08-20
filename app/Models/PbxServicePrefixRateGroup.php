<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbxServicePrefixRateGroup extends Model
{
    use HasFactory;

    public const TYPE_INBOUND = 'Inbound';
    public const TYPE_OUTBOUND = 'Outbound';

    protected $table = 'pbx_services_prefixrate_groups';
}
