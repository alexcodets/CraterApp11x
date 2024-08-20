<?php

namespace Crater\Authorize\Models;

use Crater\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Authorize extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'authorize';

    protected $guarded = ['id'];

    protected $appends = [
        'DecryptedAccountNumber',
        'DecryptedBankName',
        'DecryptedNumCheck',
        'DecryptedRoutingNumber',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getDecryptedCreditCardAttribute(): string
    {
        return $this->decryptField($this->credit_card_full);
    }

    public function getDecryptedCCVAttribute(): string
    {
        return $this->decryptField($this->card_number);
    }

    public function getDecryptedExpirationDateAttribute(): string
    {
        return $this->decryptField($this->expiration_date);
    }

    private function decryptField(?string $field): ?string
    {
        if ($field && strlen($field) > 45) {
            return Crypt::decryptString($field);
        }

        return $field;
    }

    public function getDecryptedAccountNumberAttribute(): string
    {

        if (! $this->isEmptyOrNull($this->account_number)) {
            return $this->isEmptyOrNull($this->account_number) ? '' : $this->isEncrypted($this->account_number) ? Crypt::decryptString($this->account_number) : $this->account_number;
        }

        return " ";

    }

    public function getDecryptedBankNameAttribute(): string
    {

        if (! $this->isEmptyOrNull($this->bank_name)) {
            return $this->isEncrypted($this->bank_name) ? Crypt::decryptString($this->bank_name) : $this->bank_name;
        }

        return " ";
    }

    public function getDecryptedNumCheckAttribute(): string
    {
        if (! $this->isEmptyOrNull($this->num_check)) {
            return $this->isEncrypted($this->num_check) ? Crypt::decryptString($this->num_check) : $this->num_check;
        }

        return " ";
    }

    public function getDecryptedRoutingNumberAttribute(): string
    {
        if (! $this->isEmptyOrNull($this->routing_number)) {
            return $this->isEmptyOrNull($this->routing_number) ? '' : $this->isEncrypted($this->routing_number) ? Crypt::decryptString($this->routing_number) : $this->routing_number;
        }

        return " ";
    }

    // Método auxiliar para determinar si el campo está vacío o es nulo
    protected function isEmptyOrNull($value): bool
    {

        return is_null($value) || $value === '';
    }

    // Método auxiliar para determinar si el valor está cifrado
    protected function isEncrypted($value): bool
    {
        try {
            Crypt::decryptString($value);

            return true; // Si no se lanza una excepción, no está cifrado
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return false; // Si se lanza una excepción, está cifrado
        }
    }
}
