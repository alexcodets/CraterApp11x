<?php

namespace Crater\Http\Requests;

use Crater\Models\Item;
use Crater\Rules\RelationNotExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => [
                'required'
            ],
            'ids.*' => [
                'required',
                Rule::exists('items', 'id'),
                new RelationNotExist(Item::class, 'avalaraConfigItemsDid', 'Cannot be removed while associated with Avalara Config'),
                new RelationNotExist(Item::class, 'avalaraConfigItemsCdr', 'Cannot be removed while associated with Avalara Config'),
                new RelationNotExist(Item::class, 'avalaraConfigItemsExtension', 'Cannot be removed while associated with Avalara Config'),
                new RelationNotExist(Item::class, 'avalaraConfigItemsInternational', 'Cannot be removed while associated with Avalara Config'),
                new RelationNotExist(Item::class, 'invoiceItems', 'Cannot be removed while associated with Invoice'),
                new RelationNotExist(Item::class, 'estimateItems', 'Cannot be removed while associated with Estimate'),
                // new RelationNotExist(Item::class, 'taxes', 'Cannot be removed while associated with Taxes')
            ]
        ];
    }
}
