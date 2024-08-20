<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrefixRateGroups extends Model
{
    use HasFactory;

    protected $table = 'prefixrate_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
        'type'
    ];

    public function customDestinations(): BelongsToMany
    {
        return $this->belongsToMany(ProfileInternacionalRate::class, 'international_rate_prefixrate_group', 'prefixrate_id', 'int_rate_id')
            ->whereNull('international_rate_prefixrate_group.deleted_at')
            ->withTimestamps();
    }
}
