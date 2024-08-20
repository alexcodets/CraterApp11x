<?php

namespace Crater\Models;

use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;

    protected $fillable = ['name', 'logo', 'unique_hash', 'company_identifier', 'title_header', 'subtitle_header', 'url_wallpaper_login'];

    protected $appends = ['logo', 'favicon', 'logo_base_64', 'wallpaper_login'];

    public function getLogoAttribute(): ?string
    {
        try {
            //code...
            $logo = $this->getMedia('logo')->first();

            if ($logo) {
                $isSystem = FileDisk::whereSetAsDefault(true)->first()->isSystem();

                return $isSystem ? asset($logo->getUrl()) : $logo->getFullUrl();
            }

            return null;
        } catch (\Throwable $th) {

            return null;
            //throw $th;
        }
    }

    public function getWallPaperLoginAttribute(): ?string
    {
        $url_wallpaper_login = $this->getMedia('wallpaperLogin')->first();
        if ($url_wallpaper_login) {
            return FileDisk::whereSetAsDefault(true)->first()->isSystem() ? asset($url_wallpaper_login->getUrl()) : $url_wallpaper_login->getFullUrl();
        }

        return null;
    }

    public function getLogoBase64Attribute(): ?string
    {
        try {
            //code...

            $logo = $this->getMedia('logo')->first();

            if ($logo) {
                $filePath = $logo->getPath();
                $type = File::mimeType($filePath);

                return 'data:'.$type.';base64,'.base64_encode(file_get_contents($filePath));
            }

            return null;
        } catch (\Throwable $th) {
            return null;
            //throw $th;
        }
    }

    public function getFaviconAttribute(): ?string
    {
        $favicon = $this->getMedia('favicon')->first();

        if ($favicon) {
            return FileDisk::whereSetAsDefault(true)->first()->isSystem() ? asset($favicon->getUrl()) : $favicon->getFullUrl();
        }

        return null;
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getSettingAttribute()
    {
        return $this->settings->flatMap(function ($setting) {
            return [$setting->option => $setting->value];
        });

    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function locations(): MorphToMany
    {
        return $this->morphToMany(AvalaraLocation::class, 'locationable', 'avalara_locationables', 'small_locationable_id');
    }

    /**
     * Main Location For the Invoice
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(AvalaraLocation::class, 'avalara_location_id');
    }

    public function avalaraConfiguration(): HasOne
    {
        return $this->hasOne(AvalaraConfig::class)->where('status', '=', 'A');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getExtDownEmailSettingAttribute(): array
    {
        return [
            'body' => $this->settings()->where('option', 'pbx_ext_body_down')->first('value')->value ?? null,
            'subject' => $this->settings()->where('option', 'pbx_ext_subject_down')->first('value')->value ?? null,
        ];
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function settingAllowCardExpirationPaymentJob(): HasMany
    {
        return $this->settings()->where('option', '=', 'allow_cardexpiration_payment_job')->where('value', '=', 1);
    }

    public function settingTimeRunCarExpirationPaymentJob(): HasMany
    {
        return $this->settings()->where('option', '=', 'time_run_cardexpiration_payment_job')->where('value', '=', now()->format('H:i'));
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class);
    }

    public function getKeySettingsAttribute()
    {
        return $this->settings->pluck('value', 'option');

    }

    public function getExtUpEmailSettingAttribute(): array
    {
        return [
            'body' => $this->settings()->where('option', 'pbx_ext_body_up')->first('value')->value ?? null,
            'subject' => strip_tags($this->settings()->where('option', 'pbx_ext_subject_up')->first('value')->value ?? null),
        ];
    }

    public function getServerUpEmailSettingAttribute(): array
    {
        return [
            'body' => $this->settings()->where('option', 'pbx_server_emailbody_up')->first('value')->value ?? null,
            'subject' => strip_tags($this->settings()->where('option', 'server_subject_up')->first('value')->value ?? null),
        ];
    }

    public function getServerDownEmailSettingAttribute(): array
    {
        return [
            'body' => $this->settings()->where('option', 'pbx_server_emailbody_down')->first('value')->value ?? null,
            'subject' => strip_tags($this->settings()->where('option', 'server_subject_down')->first('value')->value ?? null),
        ];
    }

    public function getCreditCardReminderEmailSettingAttribute(): array
    {
        return [
            'body' => $this->settings()->where('option', 'payment_card_expiration_reminders')->first('value')->value ?? null,
            'subject' => strip_tags($this->settings()->where('option', 'payment_card_expiration_reminders_subject')->first('value')->value ?? 'Payment Credit Card Reminder'),
        ];
    }

    public function getGeneralEmailSettingAttribute(): array
    {
        return [
            'company' => $this,
            'PRIMARY_COLOR' => $this->settings()->where('option', 'color_invoice')->first('value')->value ?? '#5851D8',
            'subject' => strip_tags($this->settings()->where('option', 'server_subject')->first('value')->value ?? null),
        ];
    }

    public function getServerCheckNotificationAttribute(): array
    {
        return [
            'server_email' => $this->settings()->where('option', 'server_notification')->first('value')->value ?? false,
            'notification_enabled' => $this->settings()->where('option', 'activate_notification')->first('value')->value ?? false
        ];
    }

    public function getCheckNotificationAttribute(): array
    {
        return [
            'server_email' => $this->settings()->where('option', 'server_notification')->first('value')->value ?? false,
            'notification_enabled' => $this->settings()->where('option', 'activate_notification')->first('value')->value ?? false
        ];
    }

    public function getIsNotificationDeactivatedAttribute(): bool
    {
        return $this->settings()->where('option', 'send_email_deactive')->first('value')->value ?? 'NO';
    }

    public function getMainEmailAttribute()
    {
        return $this->settings()->where('option', 'server_notification')->first('value')->value ?? null;
    }
}
