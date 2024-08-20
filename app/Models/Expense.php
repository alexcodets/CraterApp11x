<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Crater\Traits\ExtraInteractionWithMedia;
use Crater\Traits\HasCustomFieldsTrait;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
//
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\MediaLibrary\HasMedia;
//
//
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasCustomFieldsTrait;
    use ExtraInteractionWithMedia;

    protected $fillable = [
        'expense_number',
        'expense_date',
        'expense_category_id',
        'amount',
        'user_id',
        'notes',
        'providers_id',
        'items_id',
        'status',
        'subject',
        'notification',
        'payment_method_id',
        'payment_date',
        'store_id'
    ];

    protected $guarded = ['id'];
    public const STATUS_PENDING = 'Pending';
    public const STATUS_ACTIVE = 'Active';

    protected $appends = [
        'formattedExpenseDate',
        'formattedCreatedAt',
        'receiptPdfUrl',
        'receipt',
        'docs',
    ];

    /*  protected $fillable = [
    'attachment_docs'
    ]; */

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    /* public function setAttachmentDocsAttribute($value)
    {
    $this->attributes['attachment_docs'] = json_encode($value);
    } */

    public function setExpenseDateAttribute($value)
    {
        if ($value) {
            $this->attributes['expense_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'providers_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'creator_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function companySettings(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getFormattedExpenseDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->expense_date)->format($dateFormat);
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getReceiptAttribute($value)
    {
        $media = $this->getFirstMedia('receipts');
        if ($media) {
            return $media->getPath();
        }

        return null;
    }

    public function getReceiptPdfUrlAttribute()
    {
        if ($this->getFirstMedia('receipts') != null) {
            return url('/expenses/pdf/'.$this->id);
        }
    }

    public function getDocsAttribute($value)
    {
        $media = $this->getFirstMedia('docs');

        if ($media) {
            return $media->getPath();
        }

        return null;
    }

    public function scopeExpensesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'expense_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereCategoryName($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('category', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereNotes($query, $search)
    {
        $query->where('notes', 'LIKE', '%'.$search.'%');
    }

    public function scopeWhereCategory($query, $categoryId)
    {
        return $query->where('expenses.expense_category_id', $categoryId);
    }

    public function scopeWhereUser($query, $user_id)
    {
        return $query->where('expenses.user_id', $user_id);
    }

    public function scopeWhereProvider($query, $providers_id)
    {
        return $query->where('expenses.providers_id',  $providers_id);
    }

    public function scopeWhereSubject($query, $subject)
    {
        return $query->where('expenses.subject', 'LIKE', '%'.$subject.'%');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('expense_category_id')) {
            $query->whereCategory($filters->get('expense_category_id'));
        }

        if ($filters->get('subject')) {
            $query->whereSubject($filters->get('subject'));
        }

        if ($filters->get('user_id')) {
            $query->whereUser($filters->get('user_id'));
        }

        if ($filters->get('expense_id')) {
            $query->whereExpense($filters->get('expense_id'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->expensesBetween($start, $end);
        }

        if ($filters->get('expense_number')) {
            $query->expenseNumber($filters->get('expense_number'));
        }

        if ($filters->get('customcode')) {
            $query->expenseCustomcode($filters->get('customcode'));
        }

        if ($filters->get('providers_id')) {
            $query->WhereProvider($filters->get('providers_id'));
        }

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        // status
        if ($filters->get('status') && $filters->get('status') != 'All') {
            if($filters->get('status') == 'NextDue') {
                $days = CompanySetting::where('option', 'warning_before_due_date')->where('company_id', auth()->user()->company->id)->pluck('value')->first();
                $today = Carbon::now()->format('Y-m-d');
                $dayDue = Carbon::now()->addDay($days)->format('Y-m-d');
                $query->whereBetween('expenses.expense_date', [$today, $dayDue])->where('expenses.status', 'Pending');
            } else {
                $query->where('expenses.status', $filters->get('status'));
            }
        }

        if ($filters->get('customer')) {
            if (preg_match('~[0-9]+~', $filters->get('customer'))) {
                $filters->put('customer', explode(',', $filters->get('customer')));
                $query->whereIn('user_id', $filters->get('customer'));
            }
        }

        if ($filters->get('country')) {
            $query->whereCountry($filters->get('country'));
        }

        if ($filters->get('state')) {
            $query->whereState($filters->get('state'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'expense_date';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }


    }

    // applyFilterReport
    // estas son las variables que llegan
    // provider: [] dentro hay id [1, 2]
    // customer: [] dentro hay id [1, 2]
    // payment_mode: [] dentro hay id [1, 2]
    // category: [] dentro hay id [1, 2]
    // status: [] dentro hay id [1, 2]
    // item: [] dentro hay id [1, 2]

    public function scopeApplyFiltersReport($query, array $filters)
    {
        $filters = collect($filters);

        // los valores vienen asi "1, 2, 3" convertir a array

        if ($filters->get('provider')) {

            if (preg_match('~[0-9]+~', $filters->get('provider'))) {

                $filters->put('provider', explode(',', $filters->get('provider')));

                $query->whereIn('providers_id', $filters->get('provider'));
            }
        }

        if ($filters->get('customer')) {
            if (preg_match('~[0-9]+~', $filters->get('customer'))) {
                $filters->put('customer', explode(',', $filters->get('customer')));
                $query->whereIn('expenses.user_id', $filters->get('customer'));
            }
        }

        if ($filters->get('payment_mode')) {
            // search field payment_method_id. payment_mode is array of ids
            if (preg_match('~[0-9]+~', $filters->get('payment_mode'))) {
                $filters->put('payment_mode', explode(',', $filters->get('payment_mode')));
                $query->whereIn('expenses.payment_method_id', $filters->get('payment_mode'));
            }
        }

        if ($filters->get('category')) {
            if (preg_match('~[0-9]+~', $filters->get('category'))) {
                $filters->put('category', explode(',', $filters->get('category')));
                $query->whereIn('expenses.expense_category_id', $filters->get('category'));
            }
        }

        if ($filters->get('status')) {
            $filters->put('status', explode(',', $filters->get('status')));
            // validar si hay all en el array
            if (! in_array('all', $filters->get('status'))) {
                $query->whereIn('expenses.status', $filters->get('status'));
            }
        }

        if ($filters->get('item')) {
            if (preg_match('~[0-9]+~', $filters->get('item'))) {
                $filters->put('item', explode(',', $filters->get('item')));
                $query->whereIn('expenses.items_id', $filters->get('item'));
            }
        }
    }

    public function scopeWhereExpense($query, $expense_id)
    {
        $query->orWhere('id', $expense_id);
    }

    public function scopeExpenseNumber($query, $expenseNumber)
    {
        return $query->where('expenses.expense_number', 'LIKE', '%'.$expenseNumber.'%');
    }

    public function scopeExpenseCustomcode($query, $customcode)
    {
        // filtrar por customcode de cliente
        $query->whereHas('user', function ($query) use ($customcode) {
            $query->where('customcode', 'LIKE', '%'.$customcode.'%');
        });
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('category', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%');
            })
                ->orWhere('notes', 'LIKE', '%'.$term.'%');
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('expenses.company_id', $company_id);
    }

    public function scopewhereCountry($query, $customer_id)
    {
        $array = [];
        $array = Address::where("country_id", $customer_id)->pluck("user_id")->toarray();

        $query->whereIN('expenses.user_id', $array);
    }

    public function scopewhereState($query, $customer_id)
    {

        $array = Address::where("state_id", $customer_id)->pluck("user_id")->toarray();
        $query->whereIN('expenses.user_id', $array);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeExpensesAttributes($query)
    {
        $query->select(
            DB::raw('
                count(*) as expenses_count,
                sum(amount) as total_amount,
                expense_category_id')
        )
            ->groupBy('expense_category_id');
    }

    public static function createExpense($request)
    {
        $data = $request->validated();

        $data['creator_id'] = Auth::id();
        $data['company_id'] = $request->header('company');
        $data['payment_id'] = $request->get('payment_id');
        $data['payment_date'] = $request->get('payment_date');
        $data['payment_method_id'] = $request->get('payment_method_id');
        $data['expense_number'] = self::getExpenseNumber($request->get('expense_number'));

        if ($request->get('payment_id') != null) {
            $payment = Payment::where("id", $request->get('payment_id'))->first();
            if ($payment != null) {
                $data['user_id'] = $payment->user_id;
            }
        }

        \Log::debug($data);
        $expense = self::create($data);
        $expense->subject = $expense->subject;
        $expense->creator_id = $data['creator_id'];
        $expense->expense_number = $data['expense_number'];
        $expense->expense_date = $data['expense_date'];
        $expense->expense_category_id = $data['expense_category_id'];
        $expense->amount = $data['amount'];
        $expense->user_id = $data['user_id'];
        $expense->notes = $data['notes'];
        $expense->providers_id = $data['providers_id'];
        $expense->items_id = $data['items_id'];
        $expense->company_id = $data['company_id'];
        $expense->payment_id = $data['payment_id'];
        $expense->payment_date = $data['payment_date'];
        $expense->payment_method_id = $data['payment_method_id'];
        $expense->status = $data['status'];
        $expense->store_id = $request['store_id'];
        $expense->notification = $data['notification'] ? 1 : 0;

        $opening_date = new DateTime($expense->expense_date);
        $current_date = new DateTime();

        if ($opening_date > $current_date) {
            $expense->status = "Pending";
        }

        $expense->save();

        // expense_invoices
        $invoices = json_decode($request->invoices, true);
        if (count($invoices) > 0) {
            self::createExpenseInvoices($invoices, $expense->id, $expense->providers_id, "create");
        }
        //

        if ($request->hasFile('attachment_receipt')) {
            $expense->addMediaFromRequest('attachment_receipt')->toMediaCollection('receipts', 'local');
        }

        if ($request->attachment_docs) {
            foreach ($request->attachment_docs as $file) {
                if ($file) {
                    $expense->addMedia($file)->toMediaCollection('docs', 'local');
                }
            }
        }
        ////Log::debug($request);

        $customFields = json_decode($request->customFields, true);

        if ($customFields) {
            $expense->addCustomFields($customFields);
        }

        return $expense;
    }

    public function updateExpense($request)
    {
        $this->update($request->validated());

        // expense_invoices
        $invoices = json_decode($request->invoices, true);
        if (count($invoices) > 0) {
            self::createExpenseInvoices($invoices, $this->id, $request->providers_id, "update");
        } else {
            $expense_invoices_id = \DB::table('expense_invoices')->where('expense_id', $this->id)->pluck('id');
            \DB::table('expense_invoices_tax_types')->whereIn('expense_invoice_id', $expense_invoices_id)->delete();
            //
            \DB::table('expense_invoices')->where('expense_id', $this->id)->delete();
        }

        if ($request->attachment_receipt == 'none') {
            $this->clearMediaCollection('receipts');
        } elseif ($request->hasFile('attachment_receipt')) {
            $this->clearMediaCollection('receipts');
            $this->addMediaFromRequest('attachment_receipt')->toMediaCollection('receipts', 'local');
        }
        $this->clearMediaCollection('docs');
        if (isset($request->attachment_docs)) {
            foreach ($request->attachment_docs as $file) {
                if (isset($file->base64)) {
                    $this->addMediaFromBase64($file->base64)->toMediaCollection('docs', 'local');
                } else {
                    $this->addMedia($file)->toMediaCollection('docs', 'local');
                }
            }
        }

        $customFields = json_decode($request->customFields, true);

        if ($customFields) {
            $this->updateCustomFields($customFields);
        }

        return true;
    }

    public static function createExpenseInvoices($invoices, $expense_id, $providers_id, $mode)
    {
        try {
            if ($mode === "update") {
                $expense_invoices_id = \DB::table('expense_invoices')->where('expense_id', $expense_id)->pluck('id');
                \DB::table('expense_invoices_tax_types')->whereIn('expense_invoice_id', $expense_invoices_id)->delete();
                //
                \DB::table('expense_invoices')->where('expense_id', $expense_id)->delete();
            }

            foreach ($invoices as $invoice) {
                \DB::table('expense_invoices')->insert(
                    [
                        'expense_id' => $expense_id,
                        'provider_id' => $providers_id,
                        'invoice_number' => $invoice["invoice_number"],
                        'subtotal' => $invoice["subtotal"],
                        'total_tax' => $invoice["total_tax"],
                        'total' => $invoice["total"],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                if(count($invoice["taxes"]) > 0) {
                    $expense_invoice = DB::table('expense_invoices')->latest()->first();

                    foreach ($invoice["taxes"] as $tax) {
                        \DB::table('expense_invoices_tax_types')->insert(
                            [
                                'expense_invoice_id' => $expense_invoice->id,
                                'tax_type_id' => $tax["id"],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]
                        );
                    }
                }

            }
        } catch (\Throwable $th) {
            \Log::debug("error createExpenseInvoices");
        }
    }

    public function getExpensePrefixAttribute()
    {
        $prefix = explode("-", $this->expense_number)[0];

        return $prefix;
    }

    public function getExpenseNumAttribute()
    {
        $position = $this->strposX($this->expense_number, "-", 1) + 1;

        return substr($this->expense_number, $position);
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

    public static function getNextExpenseNumber($value): string
    {

        // Get the last created order
        $expense = Expense::where('expense_number', 'LIKE', $value.'-%')
            ->orderBy('created_at', 'desc')
            ->first();
        if (! $expense) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $expense->expense_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', intval($number) + 1);
    }

    public function getExpenseNumber($expenseNumber)
    {
        // validar si existe el numero de factura
        $expense = Expense::where('expense_number', $expenseNumber)->first();
        if (! $expense) {
            return $expenseNumber;
        } else {

            $lastExpense = Expense::where('company_id', Auth::user()->company_id)->orderBy('id', 'desc')->first();
            if ($lastExpense) {
                $lastNumber = explode('-', $lastExpense->expense_number);
                $nextNumber = $lastNumber[1] + 1;
                $nextNumber = sprintf('%06d', $nextNumber);

                return $lastNumber[0].'-'.$nextNumber;
            } else {
                return 'EXPE-1';
            }
        }
    }
}
