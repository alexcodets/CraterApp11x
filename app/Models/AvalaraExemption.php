<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvalaraExemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'frc', 'tpe', 'dom', 'cat', 'exnb', 'scp', 'avalara_locations_id', 'pbx_services_id', 'user_id', 'enable', 'exemption_name'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(AvalaraLocation::class, 'avalara_locations_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
