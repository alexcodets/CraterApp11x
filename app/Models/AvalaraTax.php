<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Log;

class AvalaraTax extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public const LVL_ARRAY = ['Federal', 'State', 'County', 'City', 'Unincorporated'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(AvalaraTaxType::class, 'avalara_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AvalaraTaxCagetory::class, 'avalara_category_id');
    }

    public function invoiceItem(): HasOne
    {
        return $this->hasOne(InvoiceItem::class, 'id', 'invoice_item_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function getLvlNameAttribute()
    {
        //Log::debug("Level - Array: ");
        //Log::debug(self::LVL_ARRAY[$this->lvl]);
        return self::LVL_ARRAY[$this->lvl] ?? 'N/A';
    }

    public function getDescriptionAttribute()
    {
        //Log::debug("Descripcion: ");
        //Log::debug("{$this->name} - {$this->lvlName}");
        return "{$this->name} - {$this->lvlName}";
    }

    //Invoice::first()->avalaraInvoice->avalaraTaxes()->with('InvoiceItem:id,name,description')->get(['id', 'invoice_item_id', 'amount', 'rate', 'tax']);
    //Invoice::first()->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax']);


}
