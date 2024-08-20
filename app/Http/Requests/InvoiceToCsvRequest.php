<?php

namespace Crater\Http\Requests;

use Crater\Models\Invoice;
use Illuminate\Validation\Rule;

class InvoiceToCsvRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'invoice_id' => 'nullable|exists:invoices,id',
            'invoice_number' => 'nullable|string',
            'customer_id' => 'nullable|exists:users,id',
            'status' => [
                'nullable',
                'string',
                Rule::in([
                    Invoice::STATUS_UNPAID,
                    Invoice::STATUS_DUE,
                    Invoice::STATUS_PARTIALLY_PAID,
                    Invoice::STATUS_PAID,
                    Invoice::STATUS_OVERDUE,
                    Invoice::STATUS_DRAFT,
                    Invoice::STATUS_SAVE_DRAFT,
                    Invoice::STATUS_VIEWED,
                    Invoice::STATUS_COMPLETED,
                    Invoice::STATUS_SENT,
                ]),
            ],
            'customcode' => 'nullable|string',
            'from_date' => 'nullable|date|before_or_equal:to_date',
            'to_date' => 'nullable|date|after_or_equal::from_date',
            'order' => 'nullable|string|in:asc,desc',
            'order_by' => 'nullable|string|in:invoice_number,invoice_date,name,status,paid_status,total,due_amount',
        ];
    }

    public function authorize(): bool
    {
        $user = auth('sanctum')->user();

        return ($user->role == 'super admin' && is_null($user->role2)) ||
            $user->role != 'customer' && $user->userPermissions()
                ->where('module', 'invoices')
                ->where('access', 1)
                ->exists();

    }
}
