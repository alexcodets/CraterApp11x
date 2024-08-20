<?php

namespace Crater\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Crater\Authorize\Models\Authorize;
use Crater\DataObject\AddressDO;
use Crater\Helpers\General;
use Crater\Jobs\GeneratePaymentPdfJob;
use Crater\Mail\PaymentAccepted;
use Crater\Mail\SendPaymentMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Log;
use Mail;
use Request;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// trait
//

class Payment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;
    use SoftDeletes;

    public const PAYMENT_MODE_CHECK = 'CHECK';
    public const PAYMENT_MODE_OTHER = 'OTHER';
    public const PAYMENT_MODE_CASH = 'CASH';
    public const PAYMENT_MODE_CREDIT_CARD = 'CREDIT_CARD';
    public const PAYMENT_MODE_BANK_TRANSFER = 'BANK_TRANSFER';

    protected $guarded = ['id'];

    protected $appends = [
        'formattedCreatedAt',
        'formattedPaymentDate',
        'paymentPdfUrl',
        'is_payment_multiple',
    ];

    protected static function booted()
    {
        static::created(function ($payment) {
            GeneratePaymentPdfJob::dispatch($payment);
        });

        static::updated(function ($payment) {
            GeneratePaymentPdfJob::dispatch($payment, true);
        });
    }

    public function setPaymentDateAttribute($value)
    {
        if ($value) {
            $this->attributes['payment_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function getPaymentGatewayAttribute(): string
    {
        if ($this->payment_paypal_id) {
            return 'Paypal';
        }

        if ($this->authorize_id) {
            return 'Authorize';
        }

        if ($this->aux_vault_id) {
            return 'AuxVault';
        }

        return 'Balance';
    }

    /**
     * Obtiene el transaction_id asociado con el pago.
     *
     * Este método verifica primero si el pago tiene un payment_paypal_id,
     * authorize_id o aux_vault_id asociado y retorna el transaction_id correspondiente.
     * Si el objeto relacionado no se encuentra o si el pago no tiene ninguno de estos IDs,
     * el método retorna 0.
     *
     * @return int|string El transaction_id o 0 si no se encuentra.
     */
    public function getTransactionIdAttribute()
    {
        // Verifica si existe payment_paypal_id y obtiene el transaction_id correspondiente.
        if ($this->payment_paypal_id) {
            $paymentPaypal = PaymentsPaypal::where('payment_id', $this->id)->first();

            return $paymentPaypal ? $paymentPaypal->transaction_id : 0;
        }

        // Verifica si existe authorize_id y obtiene el transaction_id correspondiente.
        if ($this->authorize_id) {
            $authorize = Authorize::where('payment_id', $this->id)->first();

            return $authorize ? $authorize->transaction_id : 0;
        }

        // Verifica si existe aux_vault_id y obtiene el transaction_id correspondiente.
        if ($this->aux_vault_id) {
            $auxVault = AuxVault::where('id', $this->aux_vault_id)->first();

            return $auxVault ? $auxVault->transaction_id : 0;
        }

        // Si no se encuentra ninguno de los IDs anteriores, retorna 0.
        return 0;
    }

    public function getPaymentPrefixAttribute()
    {
        return explode('-', $this->payment_number)[0];
    }

    public function getFormattedCreatedAtAttribute($value): string
    {
        return Carbon::parse($this->created_at)->format(CompanySetting::getSetting('carbon_date_format', $this->company_id));
    }

    public function getFormattedPaymentDateAttribute($value): string
    {
        return Carbon::parse($this->payment_date)->format(CompanySetting::getSetting('carbon_date_format', $this->company_id));
    }

    public function getPaymentPdfUrlAttribute()
    {
        return url('/payments/pdf/'.$this->unique_hash);
    }

    public function getPaymentNumAttribute()
    {
        return substr($this->payment_number, $this->strposX($this->payment_number, "-", 1) + 1);
    }

    public function emailLogs(): MorphMany
    {
        return $this->morphMany(\Crater\Models\EmailLog::class, 'mailable');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'creator_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'payments_payment_methods', 'payment_id', 'payment_method_id')
            ->select(['payment_method_id as id', 'name', 'amount', 'received', 'returned'])
            ->where('is_multiple', 0);
    }

    public function authorize(): BelongsTo
    {
        return $this->belongsTo(Authorize::class);
    }

    public function paymentsPaypal(): BelongsTo
    {
        // payments_paypal_id belongsTo
        return $this->belongsTo(PaymentsPaypal::class, 'payment_paypal_id', 'id');
    }

    public function auxVault(): BelongsTo
    {
        return $this->belongsTo(AuxVault::class);
    }

    public function send($data): array
    {
        $data['payment'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = Company::find($this->company_id);
        $data['body'] = $this->getEmailBody($data['body']);
        $data['subject'] = $this->removeAttributesHtml($this->getEmailBody($data['subject']));
        $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        $data['PRIMARY_COLOR'] = $colorInvoice;

        \Mail::to($data['to'])->send(new SendPaymentMail($data));
        $bbcmail = CompanySetting::select('value', 'id')->where('option', 'payment_bbc_email')->where('company_id', $this->company_id)->first();

        if ($bbcmail != null) {
            if ($bbcmail->value != "") {
                \Mail::to($bbcmail->value)->send(new SendPaymentMail($data));
            }

        }

        // envio de contactos
        $contacts = Contacts::select('email')->where('customer_id', $this->user_id)->where('allow_receive_emails', 1)->where('email_pbx_services', 1)->get();
        foreach ($contacts as $key => $email) {
            if ($email != null && $email != '') {
                // enviar email
                Mail::to($email)->send(new SendPaymentMail($data));
                // save emails logs
                // $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
            }
        }

        // enviar email a usuarios
        $listUser = User::where('role', 'super admin')->where('email_invoices', 1)->where('company_id', $this->company_id)->get();

        foreach ($listUser as $user) {
            try {
                Mail::to($user->email)->send(new SendPaymentMail($data));
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        return [
            'success' => true,
        ];
    }

    public static function createPayment($request)
    {
        $data = $request->validated();
        $data['company_id'] = 1;
        $data['creator_id'] = Auth::id();

        if (empty($data['company_id'])) {
            $data['company_id'] = 1;
        }

        if ($request->has('invoice_id') && $request->invoice_id != null) {
            $invoice = Invoice::find($request->invoice_id);

            if ($invoice && $invoice->due_amount == $request->amount) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
                $invoice->due_amount = 0;
            } elseif ($invoice && $invoice->due_amount != $request->amount) {
                $invoice->due_amount = (int) $invoice->due_amount - (int) $request->amount;
                if ($invoice->due_amount < 0) {
                    return [
                        'error' => 'invalid_amount',
                    ];
                }
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }
            $invoice->save();
        }

        $hash = General::generateUniqueId();
        $data['unique_hash'] = $hash;

        $prefix = CompanySetting::where('company_id', 1)
            ->where('option', 'payment_prefix')
            ->first()
            ->value;
        $number = self::getNextPaymentNumber($prefix);
        $prefixnumber = $prefix."-".$number;
        \Log::debug("number");
        \Log::debug($prefixnumber);
        $data['payment_number'] = $prefixnumber;
        $payment = Payment::create($data);

        /* @var Payment $payment */

        while (Payment::where('unique_hash', $hash)->count() != 1) {
            $payment->unique_hash = General::generateUniqueId();
            $payment->save();
        }

        /// cuando el payment no tiene factura asignada, se le agrega como credito al cliente
        if ($payment->invoice_id == null) {
            //Log::debug("entro a creditos");
            $User = User::where("id", $payment->user_id)->where("status_customer", "A")->where("role", "customer")->first();

            if ($User != null) {
                $balanceactual = $User->balance;
                $paymentamount = ($payment->amount / 100);

                $balance_customer = new BalanceCustomer();
                $balance_customer->status = 'A';
                $balance_customer->present_balance = $User->balance;
                $balance_customer->amount_op = $paymentamount;
                $balance_customer->amount_final = $User->balance + ($payment->amount / 100);
                $balance_customer->payment_id = $payment->id;
                $balance_customer->user_id = $User->id;
                $balance_customer->save();

                $User->balance = $User->balance + ($payment->amount / 100);
                $User->save();

            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $payment->addCustomFields($customFields);
        }

        $payment = Payment::with([
            'user',
            'invoice',
            'paymentMethod',
        ])->find($payment->id);

        return $payment;
    }

    public static function createMultiplePayment($request)
    {
        $data = $request->validated();

        $data['company_id'] = $request->header('company');
        $data['creator_id'] = Auth::id();

        if ($request->has('invoice_id') && $request->invoice_id != null) {
            $invoice = Invoice::find($request->invoice_id);

            if ($invoice && $invoice->due_amount == $request->amount) {
                // Pago Completo
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
                $invoice->due_amount = 0;
                //
            } elseif ($invoice && $invoice->due_amount != $request->amount) {
                // Pago Parcial
                $invoice->due_amount = (int) $invoice->due_amount - (int) $request->amount;
                if ($invoice->due_amount < 0) {
                    return [
                        'error' => 'invalid_amount',
                    ];
                }
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
                //
            }
            $invoice->save();
        }

        if ($request->has('is_multiple') && $request->is_multiple) {
            $payment_method_multiple = PaymentMethod::where("name", "Multiple")
                ->where("is_multiple", 1)
                ->first();

            if ($payment_method_multiple) {
                $data['payment_method_id'] = $payment_method_multiple->id;
            }
        }

        $hash = General::generateUniqueId();
        $data['unique_hash'] = $hash;
        $payment = Payment::create($data);

        // Unique Hash
        while (Payment::where('unique_hash', $hash)->count() != 1) {
            $payment->unique_hash = General::generateUniqueId();
            $payment->save();
        }

        // create records in "payments_payment_methods"
        if (isset($request->payment_methods)) {
            if (count($request->payment_methods) > 0) {
                self::createPaymentsPaymentMethods($payment, $request->payment_methods);
            }
        }

        /*
        /// cuando el payment no tiene factura asignada, se le agrega como credito al cliente
        if ($payment->invoice_id == null) {
        //Log::debug("entro a creditos");
        $User = User::where("id", $payment->user_id)->where("status_customer", "A")->where("role", "customer")->first();

        if ($User != null) {
        $balanceactual = $User->balance;
        $paymentamount = ($payment->amount / 100);

        $balance_customer = new BalanceCustomer;
        $balance_customer->status = 'A';
        $balance_customer->present_balance = $User->balance;
        $balance_customer->amount_op = $paymentamount;
        $balance_customer->amount_final = $User->balance + ($payment->amount / 100);
        $balance_customer->payment_id = $payment->id;
        $balance_customer->user_id = $User->id;
        $balance_customer->save();

        $User->balance = $User->balance + ($payment->amount / 100);
        $User->save();

        }
        }
         */
        /*
        $customFields = $request->customFields;

        if ($customFields) {
        $payment->addCustomFields($customFields);
        }
         */
        /*
        $payment = Payment::with([
        'user',
        'invoice',
        'paymentMethod',
        ])->find($payment->id);
         */
        return $payment;
    }

    public function updatePayment($request)
    {
        \Log::debug("update payment");
        $oldAmount = $this->amount;

        if ($request->has('invoice_id') && $request->invoice_id && ($oldAmount != $request->amount)) {
            $amount = (int) $request->amount - (int) $oldAmount;
            $invoice = Invoice::find($request->invoice_id);
            $invoice->due_amount = (int) $invoice->due_amount - (int) $amount;

            if ($invoice->due_amount < 0) {
                return [
                    'error' => 'invalid_amount',
                ];
            }

            if ($invoice->due_amount == 0) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
            } else {
                $invoice->status = $invoice->getPreviousStatus();
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->save();
        }

        if (! $request->isTransactionStatus) {

            if ($request->has('transaction_status') && $request->transaction_status === 'Unapply') {

                $customer = User::find($request->user_id);
                $amount = ($request->amount / 100);

                $balance_customer = new BalanceCustomer();
                $balance_customer->status = 'A';
                $balance_customer->present_balance = $customer['balance'];
                $balance_customer->amount_op = $amount;
                $balance_customer->amount_final = $customer->balance + $amount;
                $balance_customer->payment_id = $request->id;
                $balance_customer->user_id = $customer->id;
                $balance_customer->save();

                $customer->balance = $balance_customer['amount_final'];
                $customer->save();

            } elseif ($request->has('transaction_status') && $request->transaction_status === 'Void' && $request->status_with_authorize === true) {

                $customer = User::find($request->user_id);
                $amount = ($request->amount / 100);

                $balance_customer = new BalanceCustomer();
                $balance_customer->status = 'A';
                $balance_customer->present_balance = $customer['balance'];
                $balance_customer->amount_op = $amount;
                $balance_customer->amount_final = $customer->balance;
                $balance_customer->payment_id = $request->id;
                $balance_customer->user_id = $customer->id;
                $balance_customer->save();

                $customer->balance = $balance_customer['amount_final'];
                $customer->save();

            } elseif ($request->has('transaction_status') && $request->transaction_status === 'Refunded' && $request->status_with_authorize === true) {

                $customer = User::find($request->user_id);
                $amount = ($request->amount / 100);

                $balance_customer = new BalanceCustomer();
                $balance_customer->status = 'A';
                $balance_customer->present_balance = $customer['balance'];
                $balance_customer->amount_op = $amount;
                $balance_customer->amount_final = $customer->balance;
                $balance_customer->payment_id = $request->id;
                $balance_customer->user_id = $customer->id;
                $balance_customer->save();

                $customer->balance = $balance_customer['amount_final'];
                $customer->save();

            }

        }

        $data = $request->all();

        $this->update($data);

        $customFields = $request->customFields;

        if ($customFields) {
            $this->updateCustomFields($customFields);
        }

        $payment = Payment::with([
            'user',
            'invoice',
            'paymentMethod',
        ])
            ->find($this->id);

        return $payment;
    }

    public static function deletePayments($ids)
    {
        foreach ($ids as $id) {
            $payment = Payment::find($id);

            if ($payment->invoice_id != null) {
                $invoice = Invoice::find($payment->invoice_id);
                $invoice->due_amount = ((int) $invoice->due_amount + (int) $payment->amount);

                if ($invoice->due_amount == $invoice->total) {
                    $invoice->paid_status = Invoice::STATUS_UNPAID;
                } else {
                    $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
                }

                $invoice->status = $invoice->getPreviousStatus();
                $invoice->save();
            }

            $payment->delete();
        }

        return true;
    }

    public static function createPaymentsPaymentMethods($payment, $payment_methods)
    {
        /*
        if($mode === "update")
        {
        \DB::table('invoice_pbx_did_detail')->where('invoice_id', $invoice_id)->delete();
        }
         */

        foreach ($payment_methods as $payment_method) {
            if ($payment_method["valid"]) {
                \DB::table('payments_payment_methods')->insert(
                    [
                        'payment_id' => $payment->id,
                        'payment_method_id' => $payment_method["id"],
                        'amount' => $payment_method["amount"],
                        'received' => $payment_method["received"],
                        'returned' => $payment_method["returned"],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }

        }
    }

    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public static function getNextPaymentNumber($prefix)
    {

        $lastNumber = Payment::where('payment_number', 'LIKE', "$prefix-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(payment_number, '-', -1) AS UNSIGNED) DESC")
            ->value('payment_number');

        // \Log::debug($lastNumber);
        // Obtener el número siguiente único
        $nextNumber = $lastNumber ? intval(substr($lastNumber, strlen($prefix) + 1)) + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Verificar la unicidad del número generado
        $existingInvoice = Payment::where('payment_number', "$prefix-$formattedNumber")->exists();

        // Si el número generado ya existe, buscar el siguiente número único
        while ($existingInvoice) {
            $nextNumber++;
            $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $existingInvoice = Payment::where('payment_number', "$prefix-$formattedNumber")->exists();
        }

        return $formattedNumber;
    }

    public function scopePaymentsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'payments.payment_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('user', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('contact_name', 'LIKE', '%'.$term.'%')
                    ->orWhere('company_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeAssociatedPayments($query, self $payment)
    {
        return self::query()
            ->when($payment->payment_paypal_id, function ($query) use ($payment) {
                return $query->where('payment_paypal_id', $payment->payment_paypal_id);
            })
            ->when($payment->authorize_id, function ($query) use ($payment) {
                return $query->where('authorize_id', $payment->authorize_id);
            })
            ->when($payment->aux_vault_id, function ($query) use ($payment) {
                return $query->where('aux_vault_id', $payment->aux_vault_id);
            });
    }

    public function scopePaymentNumber($query, $paymentNumber)
    {
        return $query->where('payments.payment_number', 'LIKE', '%'.$paymentNumber.'%');
    }

    public function scopeWhereInvoiceNumber($query, $invoiceNumber)
    {
        $query->whereHas('invoice', function ($query) use ($invoiceNumber) {
            $query->where('invoice_number', 'LIKE', '%'.$invoiceNumber.'%');
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('payments.transaction_status', 'LIKE', '%'.$status.'%');
    }

    public function scopePaymentMethod($query, $paymentMethodId)
    {
        return $query->where('payments.payment_method_id', $paymentMethodId);
    }

    public function scopeAuthorize($query, $AuthorizeId)
    {
        return $query->where('payments.authorize_id', $AuthorizeId);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('payment_number')) {
            $query->paymentNumber($filters->get('payment_number'));
        }

        if ($filters->get('invoice_number')) {
            $query->whereInvoiceNumber($filters->get('invoice_number'));
        }

        if ($filters->get('transaction_status')) {
            if ($filters->get('transaction_status') != "ALL") {
                if ($filters->get('transaction_status') != "balance_to_debit") {
                    $query->status($filters->get('transaction_status'));
                } else {
                    $query->whereNotNUll('invoice_id')->whereNUll('payment_method_id');

                }
            }
        }

        if ($filters->get('payment_id')) {
            $query->wherePayment($filters->get('payment_id'));
        }

        if ($filters->get('payment_method_id')) {
            $query->paymentMethod($filters->get('payment_method_id'));
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('customer')) {

            if (preg_match('~[0-9]+~', $filters->get('customer'))) {
                $filters->put('customer', explode(',', $filters->get('customer')));
                $query->WhereCustomerArray($filters->get('customer'));
            }
        }

        if ($filters->get('country')) {
            $query->whereCountry($filters->get('country'));
        }

        if ($filters->get('state')) {
            $query->whereState($filters->get('state'));
        }

        if ($filters->get('customcode')) {
            $query->whereCustomcode($filters->get('customcode'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->PaymentsBetween($start, $end);
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'payment_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWherePayment($query, $payment_id)
    {
        $query->orWhere('id', $payment_id);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('payments.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('payments.user_id', $customer_id);
    }

    public function scopeWhereCustomerArray($query, $customer_id)
    {
        $query->whereIN('payments.user_id', $customer_id);
    }

    public function scopewhereCountry($query, $customer_id)
    {
        $array = [];
        $array = Address::where("country_id", $customer_id)->pluck("user_id")->toarray();
        $query->whereIN('payments.user_id', $array);
    }

    public function scopewhereState($query, $customer_id)
    {

        $array = Address::where("state_id", $customer_id)->pluck("user_id")->toarray();
        $query->whereIN('payments.user_id', $array);
    }

    public function scopeWhereCustomcode($query, $customcode)
    {
        // filtrar por customcode de cliente
        $query->whereHas('user', function ($query) use ($customcode) {
            $query->where('customcode', 'LIKE', '%'.$customcode.'%');
        });
    }

    public function getPDFData()
    {

        $received = $returned = 0;

        if ($this->paymentMethods->isNotEmpty()) {
            $payments = \DB::table('payments_payment_methods')
                ->where('payment_id', $this->id)
                ->get(['received', 'returned']);

            $received = $payments->sum('received');
            $returned = $payments->sum('returned');
        }

        $company = Company::find($this->company_id);
        $logo = $company->logo_base_64 ?? null;

        ///  payments asociated
        $listpay = collect();
        $authorizeobjet = null;
        $authorizeobjet = Authorize::where("id", $this->authorize_id)->first();
        // Definir un array con las condiciones basadas en los valores no nulos
        $conditions = [
            'authorize_id' => $this->authorize_id,
            'aux_vault_id' => $this->aux_vault_id,
            'payment_paypal_id' => $this->payment_paypal_id,
        ];

        // Filtrar las condiciones para mantener solo las que no son nulas
        $filteredConditions = array_filter($conditions, function ($value) {
            return ! is_null($value);
        });

        $paymentfees = [];
        // Si hay condiciones no nulas, ejecutar la consulta
        if (! empty($filteredConditions)) {
            // Obtener la clave y el valor de la primera condición no nula
            $key = key($filteredConditions);
            $value = reset($filteredConditions);

            $query = Payment::query();

            foreach ($conditions as $conditionKey => $conditionValue) {
                if (! is_null($conditionValue)) {
                    $query->where($conditionKey, $conditionValue);
                    \Log::debug($conditionKey);
                    \Log::debug($conditionValue);
                    $paymentfees = TransactionFees::where($conditionKey, $conditionValue)->get()->toArray();
                } else {
                    $query->whereNull($conditionKey);
                }
            }

            $listpay = $query->whereNotIn('id', [$this->id])->get();
        }

        \Log::debug($paymentfees);

        $totalGeneralfees = 0;

        if (! empty($paymentfees)) {
            foreach ($paymentfees as $paymentfee) {
                $totalGeneralfees += $paymentfee['total'];
            }

        }
        \Log::debug($totalGeneralfees);

        $payarray = $listpay->isNotEmpty() ? $listpay->map(function ($pay) {
            $responsible = $pay->creator_id ? DB::table('users')->where('id', $pay->creator_id)->first() : null;
            $paymentMethod = $pay->payment_method_id ? PaymentMethod::find($pay->payment_method_id) : null;

            return [
                'payment_date' => $pay->payment_date,
                'payment_number' => $pay->payment_number,
                'transaction_status' => $pay->transaction_status,
                'responsible' => $responsible,
                'amount' => $pay->amount,
                'payment_method' => $paymentMethod ? $paymentMethod->name : 'Customer Credit Balance',
            ];
        })->toArray() : [];

        $totalfeearray["totalgeneralfees"] = $totalGeneralfees;
        ///
        view()->share([
            'payment' => $this,
            'authorizeobjet' => $authorizeobjet,
            'company_address' => $this->getCompanyAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'logo' => $logo,
            'PaymentsArray' => $payarray,
            'paymentfees' => $paymentfees,
            'totalGeneralfees' => $totalfeearray,

            // received and returned
            'received' => $received,
            'returned' => $returned,
            'responsible' => \DB::table('users')->where('id', $this->creator_id)->get(),
        ]);

        return PDF::loadView('app.pdf.payment.payment');
    }

    public function getCompanyAddress()
    {
        $format = CompanySetting::getSetting('payment_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        $format = CompanySetting::getSetting('payment_from_customer_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getNotes()
    {
        return $this->getFormattedString($this->notes);
    }

    public function getEmailBody($body)
    {
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($body, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getExtraFields()
    {

        $transaction = "N/A";
        $card_number = "N/A";
        if ($this->authorize_id != null) {
            $obj = Authorize::where("id", $this->authorize_id)->first();
            if ($obj != null) {
                $transaction = $obj->transaction_id;
                $card_number = $obj->card_number;
            }
        }

        if ($this->aux_vault_id != null) {
            $obj = AuxVault::where("id", $this->aux_vault_id)->first();
            if ($obj != null) {
                $transaction = $obj->transaction_id;
                $card_number = $obj->card_number;
            }
        }

        if ($this->payment_paypal_id != null) {
            $obj = PaymentsPaypal::where("id", $this->payment_paypal_id)->first();
            if ($obj != null) {
                $transaction = $obj->transaction_id;
                $card_number = $obj->card_number;
            }
        }

        return [
            '{PAYMENT_DATE}' => $this->formattedPaymentDate,
            '{PAYMENT_MODE}' => $this->paymentMethod ? $this->paymentMethod->name : null,
            '{PAYMENT_NUMBER}' => $this->payment_number,
            '{PAYMENT_AMOUNT}' => number_format($this->amount / 100, 2),

            '{PAYMENT_AUTHORIZE}' => $this->authorize,
            '{PAYMENT_USER}' => $this->user,
            '{TRANSACTION}' => $transaction,
            '{CARD_NUMBER}' => $card_number,
        ];

    }

    public function getIsPaymentMultipleAttribute()
    {
        $is_payment_multiple = 0;
        if ($this->payment_method_id != null) {
            $is_payment_multiple = $this->paymentMethod->is_multiple;
        }

        return $is_payment_multiple;
    }

    public function sendSuccessPaymentMail()
    {
        Log::debug('Start Sending Mail');
        $this->paymentSuccess($this);
    }

    public function paymentSuccess($payment)
    {
        if ($payment != null) {
            if ($payment->authorize_id != null) {

                $obj = Authorize::where("id", $payment->authorize_id)->first();
                $customer = User::where("id", $payment->user_id)->first();

                Log::debug('');
                if ($obj != null && $customer != null) {
                    $message = null;
                    $mode = "N/A";
                    $title = "Payment Accepted";
                    if ($obj->ACH_type != null) {
                        Log::debug('');

                        $message = CompanySetting::where("company_id", $payment->company_id)->where("option", "payment_approved_ach")->first();
                        $newTitle = CompanySetting::where("company_id", $payment->company_id)->where("option", "payment_approved_ach_subject")->first();
                        $mode = "ACH";
                        $title = "Payment approved with  ACH";

                        if ($newTitle != null) {
                            $title = $newTitle->value;
                        }
                    } else {

                        $message = CompanySetting::where("company_id", $payment->company_id)->where("option", "payment_approved_credit_card")->first();
                        $newTitle = CompanySetting::where("company_id", $payment->company_id)->where("option", "payment_approved_credit_card_subject")->first();
                        $mode = "Credit Card";
                        $title = "Payment approved with  Credit Card";
                        if ($newTitle != null) {
                            $title = $newTitle->value;
                        }
                    }

                    if ($message != null) {
                        $add = AddressDO::getAddress();
                        $company = Company::where("id", $payment->company_id)->first();

                        $newMessage = $message->value;
                        $array = [];
                        $array["PRIMARY_CONTACT_NAME"] = $customer->name;
                        $array["PRIMARY_COLOR"] = $this->getPrimaryColor($company->id);
                        $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
                        $array["CONTACT_EMAIL"] = $customer->email;
                        $array["CONTACT_PHONE"] = $customer->phone;
                        $array["CONTACT_WEBSITE"] = $customer->website;
                        $array["COMPANY_NAME"] = $company->name;
                        $array["COMPANY_COUNTRY"] = $add->country;
                        $array["COMPANY_STATE"] = $add->state;
                        $array["STATE_CODE"] = $add->state_code;
                        $array["COMPANY_CITY"] = $add->city;
                        $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
                        $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
                        $array["COMPANY_PHONE"] = $add->phone;
                        $array["COMPANY_ZIP_CODE"] = $add->zip;
                        $array["PAYMENT_DATE"] = $payment->payment_date;
                        $array["PAYMENT_NUMBER"] = $payment->payment_number;
                        $array["PAYMENT_MODE"] = $mode;
                        $array["PAYMENT_AMOUNT"] = ($payment->amount / 100);
                        $array["PAYMENT_AMOUNT"] = number_format($array["PAYMENT_AMOUNT"], 2, '.', '');
                        $array["CUSTOMER_LOGIN"] = Request::root().'/login';

                        $array["CARD_NUMBER"] = "";
                        $array["CREDIT_CARD"] = "";
                        $array["EXPIRATION_DATE"] = "";
                        // $array["EXPIRATION_DATE"] = $payment->payment_number;
                        //Log::debug('linea 675');
                        if ($obj->card_number != null) {
                            $array["CARD_NUMBER"] = $obj->card_number;

                        }
                        //Log::debug('linea 680');

                        if ($obj->credit_card != null) {
                            $array["CREDIT_CARD"] = $obj->credit_card;

                        }
                        if ($obj->expiration_date != null) {

                            $array["EXPIRATION_DATE"] = Crypt::decryptString($obj->expiration_date);

                        }
                        Log::debug('');

                        $array["TRANSACTION"] = $obj->transaction_id;
                        $array["PAYMENT_LINK"] = $payment->paymentPdfUrl;
                        // EMAIL BODY
                        //Log::debug('linea 690');
                        $newMessage = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newMessage);
                        $newMessage = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newMessage);
                        $newMessage = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newMessage);
                        $newMessage = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newMessage);
                        $newMessage = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newMessage);
                        $newMessage = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newMessage);
                        $newMessage = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newMessage);
                        $newMessage = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newMessage);
                        $newMessage = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newMessage);
                        $newMessage = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newMessage);
                        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newMessage);
                        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newMessage);
                        $newMessage = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newMessage);
                        $newMessage = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newMessage);
                        $newMessage = str_replace("{PAYMENT_DATE}", $array["PAYMENT_DATE"], $newMessage);
                        $newMessage = str_replace("{PAYMENT_NUMBER}", $array["PAYMENT_NUMBER"], $newMessage);
                        $newMessage = str_replace("{PAYMENT_MODE}", $array["PAYMENT_MODE"], $newMessage);
                        $newMessage = str_replace("{PAYMENT_AMOUNT}", $array["PAYMENT_AMOUNT"], $newMessage);
                        $newMessage = str_replace("{PAYMENT_LINK}", $array["PAYMENT_LINK"], $newMessage);
                        $newMessage = str_replace("{TRANSACTION}", $array["TRANSACTION"], $newMessage);

                        $newMessage = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newMessage);
                        $newMessage = str_replace("{CARD_NUMBER}", $array["CARD_NUMBER"], $newMessage);
                        $newMessage = str_replace("{CREDIT_CARD}", $array["CREDIT_CARD"], $newMessage);
                        $newMessage = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newMessage);
                        //  $newMessage = str_replace("{EXPIRATION_DATE}", $array["EXPIRATION_DATE"], $newMessage);
                        //Log::debug('linea 716');
                        // EMAIL SUBJECT
                        $title = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $title);
                        $title = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $title);
                        $title = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $title);
                        $title = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $title);
                        $title = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $title);
                        $title = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $title);
                        $title = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $title);
                        $title = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $title);
                        $title = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $title);
                        $title = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $title);
                        $title = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $title);
                        $title = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $title);
                        $title = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $title);
                        $title = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $title);
                        $title = str_replace("{PAYMENT_DATE}", $array["PAYMENT_DATE"], $title);
                        $title = str_replace("{PAYMENT_NUMBER}", $array["PAYMENT_NUMBER"], $title);
                        $title = str_replace("{PAYMENT_MODE}", $array["PAYMENT_MODE"], $title);
                        $title = str_replace("{PAYMENT_AMOUNT}", $array["PAYMENT_AMOUNT"], $title);
                        $title = str_replace("{PAYMENT_LINK}", $array["PAYMENT_LINK"], $title);
                        $title = str_replace("{TRANSACTION}", $array["TRANSACTION"], $title);

                        $title = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $title);
                        $title = str_replace("{CARD_NUMBER}", $array["CARD_NUMBER"], $title);
                        $title = str_replace("{CREDIT_CARD}", $array["CREDIT_CARD"], $title);
                        $title = str_replace("{STATE_CODE}", $array["STATE_CODE"], $title);

                        //Log::debug('linea 743');
                        $data['company'] = $company;

                        $data['PRIMARY_COLOR'] = $this->getPrimaryColor($company->id);

                        try {
                            //Log::debug('linea 748');
                            $title = $this->removeAttributesHtml($title);
                            Mail::to($array["CONTACT_EMAIL"])->send(new PaymentAccepted($title, $newMessage, $data));

                            // enviar correo a usuarios
                            $listUser = User::where('role', 'super admin')->where('email_payments', 1)->where('company_id', $this->company_id)->get();
                            //Log::debug('List users', [$listUser]);
                            foreach ($listUser as $user) {
                                try {
                                    Mail::to($user->email)->send(new PaymentAccepted($title, $newMessage, $data));
                                } catch (\Throwable $th) {
                                    Log::error($th);
                                }
                            }
                            $mailable_id = $message->id;
                            // save emails logs
                            // dev.DEBUG: Error: Cannot instantiate trait Crater\Traits\SendEmailsTrait in /var/www/app/Models/Payment.php:787 SOLUCIONAR por eso se comenta
                            // try{
                            //     $emailTrait = new SendEmailsTrait;
                            //     $emailTrait->saveEmailLog($customer->email, $title, $newMessage, $mailable_id, $company->id, $customer->id);
                            // }catch(\Throwable $th){
                            //     Log::error($th);
                            // }

                            $bbcmail = CompanySetting::select('value', 'id')->where('option', 'payment_bbc_email')->where('company_id', $this->company_id)->first();

                            if ($bbcmail != null) {
                                if ($bbcmail->value != "") {
                                    Mail::to($bbcmail->value)->send(new PaymentAccepted($title, $newMessage, $data));
                                }

                            }
                            Log::debug('Envio de contactos');
                            // envio de contactos
                            $contacts = Contacts::select('email')->where('customer_id', $customer->id)->where('allow_receive_emails', 1)->where('email_payments', 1)->get();
                            foreach ($contacts as $key => $email) {
                                if ($email != null && $email != '') {
                                    // enviar email
                                    Mail::to($email)->send(new PaymentAccepted($title, $newMessage, $data));
                                    // save emails logs
                                    // $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
                                }
                            }

                        } catch (Exception $ex) {
                            Log::debug('linea 834');
                            Log::debug($ex->getMessage());
                            Log::debug($ex->getTraceAsString());

                            // jump to this part
                            // if an exception occurred

                        }
                    }

                }

            }
        }

    }

    public function removeAttributesHtml($string)
    {
        $temp = str_replace('<p>', '', $string);
        $temp = str_replace('</p>', '', $temp);
        $temp = str_replace('<strong>', '', $temp);
        $temp = str_replace('</strong>', '', $temp);
        $temp = str_replace('<em>', '', $temp);
        $temp = str_replace('</em>', '', $temp);
        $temp = str_replace('<s>', '', $temp);
        $temp = str_replace('</s>', '', $temp);
        $temp = str_replace('<u>', '', $temp);
        $temp = str_replace('</u>', '', $temp);
        $temp = str_replace('<code>', '', $temp);
        $temp = str_replace('</code>', '', $temp);
        $temp = str_replace('<h1>', '', $temp);
        $temp = str_replace('</h1>', '', $temp);
        $temp = str_replace('<h2>', '', $temp);
        $temp = str_replace('</h2>', '', $temp);
        $temp = str_replace('<h3>', '', $temp);
        $temp = str_replace('</h3>', '', $temp);
        $temp = str_replace('<ul>', '', $temp);
        $temp = str_replace('</ul>', '', $temp);
        $temp = str_replace('<li>', '', $temp);
        $temp = str_replace('</li>', '', $temp);
        $temp = str_replace('<ol>', '', $temp);
        $temp = str_replace('</ol>', '', $temp);
        $temp = str_replace('<blockquote>', '', $temp);
        $temp = str_replace('</blockquote>', '', $temp);
        $temp = str_replace('<pre>', '', $temp);
        $temp = str_replace('</pre>', '', $temp);

        return $temp;
    }
}
