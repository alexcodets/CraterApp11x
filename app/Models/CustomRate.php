<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRate extends Model
{
    use HasFactory;

    protected $table = 'international_rate';

    protected $fillable = ['company_id', 'country_id', 'creator_id', 'status', 'rate_per_minute', 'prefix', 'category'];
    public const TYPE_CUSTOM_PREFIX = 'P';
    public const TYPE_CUSTOM_FROMTO = 'FT';

    public const CATEGORY_CUSTOM = 'C';
    public const CATEGORY_INTERNATIONAL = 'I';
    public const CATEGORY_TOLL_FREE = 'T';

    public function scopeOrderByLength($query, $order = 'desc')
    {
        return $query->orderByRaw('CHAR_LENGTH(prefix) desc');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public static function getCategoryName($category): string
    {
        switch ($category) {
            case self::CATEGORY_CUSTOM:
                return 'Custom Destination';
            case self::CATEGORY_INTERNATIONAL:
                return 'International Destination';
            case self::CATEGORY_TOLL_FREE:
                return 'Toll Free Destination';
            default:
                return 'Wrong Category Code';
        }

    }
}
