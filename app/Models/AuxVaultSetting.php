<?php

namespace Crater\Models;

use Crypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuxVaultSetting extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'api_key',
        'merchant_id',
        'currency',
        'default',
        'production',
        'user_id',
        'company_id',
        'enable_identification_verification',
        'enable_fee_charges',
        'name'
    ];

    protected $appends = [
        'MerchantIdDecrypted',
        'ApiKeyDecrypted'

    ];

    public function setApiKeyAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        $this->attributes['api_key'] = Crypt::encryptString($value);

    }

    protected function getApiKeyDecryptedAttribute(): string
    {
        return Crypt::decryptString($this->api_key);
    }

    public function setMerchantIdAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        $this->attributes['merchant_id'] = Crypt::encryptString($value);

    }

    protected function getMerchantIdDecryptedAttribute(): ?string
    {
        return $this->merchant_id ? Crypt::decryptString($this->merchant_id) : null;
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }
}
