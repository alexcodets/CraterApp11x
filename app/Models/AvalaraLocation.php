<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AvalaraLocation extends Model
{
    use HasFactory;

    public const PCODE = 0;
    public const FIPS = 1;
    public const NPANXX = 2;
    public const GEO = 3;
    public const ADDRESS = 4;

    protected $fillable = [
        'county',
        'country',
        'state',
        'city',
        'address',
        'zip',
        'incorporated',
        'pcd',
        'fips',
        'npa',
        'geo',
        'type',
        'user_id',
        'company_id',
        'addresses_id',
    ];

    public function getLocationAttribute(): array
    {
        switch ($this->type) {
            case self::PCODE:
                return ['pcd' => $this->pcd];

                break;
            case self::FIPS:
                return ['fips' => $this->fips];

                break;
            case self::NPANXX:
                return ['npa' => $this->npa];

                break;

            default:
                return array_filter([
                    'cnty' => $this->county,
                    'ctry' => $this->country,
                    'int' => $this->incorporated,
                    'geo' => $this->type == self::GEO ? true : null,
                    'addr' => $this->address,
                    'city' => $this->city,
                    'st' => $this->state,
                    'zip' => $this->zip,
                ]);

                break;
        }
    }

    public function defaultUser(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class);
        //return $this->morphedByMany(User::class, 'locationable', 'avalara_locationables', 'small_locationable_id');
    }

    public function defaultCompany(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
        //return $this->morphOne(User::class, 'locationable');

    }
}
