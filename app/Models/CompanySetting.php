<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'option', 'value'];

    public const LATE_FEE_TYPE_FIXED = 'fixed';
    public const LATE_FEE_TYPE_PERCENTAGE = 'percentage';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    public static function setSettings($settings, $company_id)
    {
        foreach ($settings as $key => $value) {
            if ($key === 'packages_prefix_general' || $key === 'did_prefix_general' || $key === 'extension_prefix_general' || $key === 'provider_prefix_general') {
                break;
            }
            self::updateOrCreate(
                [
                    'option' => $key,
                    'company_id' => $company_id,
                ],
                [
                    'option' => $key,
                    'company_id' => $company_id,
                    'value' => $value
                ]
            );
        }
    }

    public static function getSettings($settings, $company_id)
    {
        $settings = static::whereIn('option', $settings)->whereCompany($company_id)->get();
        $companySettings = [];

        foreach ($settings as $setting) {
            $companySettings[$setting->option] = $setting->value;
        }

        return $companySettings;
    }

    public static function getSetting($key, $company_id)
    {
        $setting = static::whereOption($key)->whereCompany($company_id)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return null;
        }
    }
}
