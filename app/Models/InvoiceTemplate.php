<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'view', 'name'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getPathAttribute($value)
    {
        return url($value);
    }
}
