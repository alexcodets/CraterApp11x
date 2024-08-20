<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\Payment */
class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'payment_date' => $this->payment_date,
            'notes' => $this->notes,
            'amount' => $this->amount,
            'unique_hash' => $this->unique_hash,
            'credit_card' => $this->credit_card,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transaction_status' => $this->transaction_status,
            'payment_prepaid' => $this->payment_prepaid,
            'inv_expense_credit' => $this->inv_expense_credit,
            'applied_credit_customer' => $this->applied_credit_customer,
            'fields_count' => $this->fields_count,
            'formatted_created_at' => $this->formatted_created_at,
            'formatted_payment_date' => $this->formatted_payment_date,
            'payment_num' => $this->payment_num,
            'payment_pdf_url' => $this->payment_pdf_url,
            'payment_prefix' => $this->payment_prefix,
            'media_count' => $this->media_count,
            'is_payment_multiple' => $this->is_payment_multiple,
            'payment_gateway' => $this->payment_gateway,
            'transaction_id' => $this->transaction_id,
            'payment_methods_count' => $this->payment_methods_count,

            'user_id' => $this->user_id,
            'invoice_id' => $this->invoice_id,
            'company_id' => $this->company_id,
            'payment_method_id' => $this->payment_method_id,
            'authorize_id' => $this->authorize_id,
            'payment_paypal_id' => $this->payment_paypal_id,
            'creator_id' => $this->creator_id,
            'aux_vault_id' => $this->aux_vault_id,
        ];
    }
}
