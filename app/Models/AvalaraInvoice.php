<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvalaraInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'pbx_service_id',
        'document_code',
        'avalara_invoice_number'
    ];

    public const STATUS_DRAFT = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_VOID = 2;
    public const STATUS_REVERSE = 3;
    public const STATUS_EDITED = 4;

    public $timestamps = false;

    public function avalaraTaxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AvalaraTax::class);
    }

    public function avalaraTaxesFor($var = null)
    {
        # code...
    }

    public function avalaraTaxesByType()
    {
        # code...
    }

    public function FunctionName()
    {
        $ext = Item::Extension()->first();
        $did = Item::Did()->first();
        $cdr = Item::Cdr()->first();
        //AvalaraInvoice::first()->avalaraTaxes()->where('item_id', 9)->first()
        //AvalaraInvoice::first()->avalaraTaxes()->where('item_id', Item::Extension()->first('id'))->first()
        //AvalaraInvoice::first()->avalaraTaxes()->where('item_id', Item::Extension()->first('id')->id)->first()
    }
}
