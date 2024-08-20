<?php

namespace Crater\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Cache;
use Carbon\Carbon;
use Crater\Helpers\General;
use Crater\Mail\SendEstimateMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Log;
use Mail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Estimate extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use HasCustomFieldsTrait;
    use SoftDeletes;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $appends = [
        'formattedExpiryDate',
        'formattedEstimateDate',
        'estimatePdfUrl',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'tax' => 'integer',
            'sub_total' => 'integer',
            'discount' => 'float',
            'discount_val' => 'integer',
        ];
    }

    public function setEstimateDateAttribute($value)
    {
        if ($value) {
            $this->attributes['estimate_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function setExpiryDateAttribute($value)
    {
        if ($value) {
            $this->attributes['expiry_date'] = Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function getEstimatePdfUrlAttribute()
    {
        return url('/estimates/pdf/'.$this->unique_hash);
    }

    public static function getNextEstimateNumber($value)
    {
        // Get the last created order
        $lastOrder = Estimate::where('estimate_number', 'LIKE', $value.'-%')
            ->withTrashed()
            ->orderBy('estimate_number', 'desc')
            ->first();

        if (! $lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode("-", $lastOrder->estimate_number);
            $number = $number[1];
        }

        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', intval($number) + 1);
    }

    public function emailLogs(): MorphMany
    {
        return $this->morphMany(\Crater\Models\EmailLog::class, 'mailable');
    }

    public function items(): HasMany
    {
        return $this->hasMany(\Crater\Models\EstimateItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'assigne_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'creator_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\Company::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function estimateTemplate(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\EstimateTemplate::class);
    }

    // relacion de uno a muchos con la tabla users_estimates
    public function assingUsersGroups(): BelongsToMany
    {
        // use deleted_at to soft delete
        return $this->belongsToMany(\Crater\Models\User::class, 'users_estimates', 'estimate_id', 'user_id')->whereNull('users_estimates.deleted_at')->withTimestamps();

    }

    // relacion uno a uno con la tabla users
    public function assingUser(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'assigne_user_id');
    }

    public function getEstimateNumAttribute()
    {
        $position = $this->strposX($this->estimate_number, "-", 1) + 1;

        return substr($this->estimate_number, $position);
    }

    public function getEstimatePrefixAttribute()
    {
        $prefix = explode("-", $this->estimate_number)[0];

        return $prefix;
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

    public function getFormattedExpiryDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->expiry_date)->format($dateFormat);
    }

    public function getFormattedEstimateDateAttribute($value)
    {
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->estimate_date)->format($dateFormat);
    }

    public function scopeEstimatesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'estimates.estimate_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('estimates.status', $status);
    }

    public function scopeWhereTotal($query, $total)
    {
        return $query->where('estimates.total', $total);
    }

    public function scopeWhereExpiryDate($query, $expiry_date)
    {
        return $query->where('estimates.expiry_date', $expiry_date);
    }

    public function scopeWhereEstimateDate($query, $estimate_date)
    {
        return $query->where('estimates.expiry_date', $estimate_date);
    }

    public function scopeWhereEstimateNumber($query, $estimateNumber)
    {
        return $query->where('estimates.estimate_number', "like", "%".$estimateNumber."%");
    }

    public function scopeWhereEstimate($query, $estimate_id)
    {
        return $query->orWhere('id', $estimate_id);
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

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('estimate_number')) {
            $query->whereEstimateNumber($filters->get('estimate_number'));
        }

        //
        if ($filters->get('expiry_date')) {
            $query->scopeWhereExpiryDate($filters->get('expiry_date'));
        }

        if ($filters->get('estimate_date')) {
            $query->scopeWhereEstimateDate($filters->get('estimate_date'));
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('status')) {
            $query->whereStatus($filters->get('status'));
        }

        if ($filters->get('total')) {
            $query->whereTotal($filters->get('total'));
        }
        //

        if ($filters->get('estimate_id')) {
            $query->whereEstimate($filters->get('estimate_id'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->estimatesBetween($start, $end);
        }

        if ($filters->get('customcode')) {
            $query->whereCustomcode($filters->get('customcode'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'estimate_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

        return $query;
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('estimates.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('estimates.user_id', $customer_id);
    }

    public function scopeWhereCustomcode($query, $customcode)
    {
        // filtrar por customcode de cliente
        $query->whereHas('user', function ($query) use ($customcode) {
            $query->where('customcode', 'LIKE', '%'.$customcode.'%');
        });
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createEstimate($request)
    {
        $data = $request->except(['items', 'taxes']);

        $data['creator_id'] = Auth::id();
        $data['status'] = self::STATUS_DRAFT;
        $data['unique_hash'] = General::generateUniqueId();
        $data['company_id'] = $request->header('company');

        $data['tax_per_item'] = CompanySetting::getSetting(
            'tax_per_item',
            $request->header('company')
        ) ?? 'NO';

        $data['discount_per_item'] = CompanySetting::getSetting(
            'discount_per_item',
            $request->header('company')
        ) ?? 'NO';

        if ($request->has('estimateSend')) {
            $data['status'] = self::STATUS_SENT;
        }

        $estimate = self::create($data);

        while (Estimate::where('unique_hash', $estimate->unique_hash)->count() != 1) {
            $estimate->unique_hash = General::generateUniqueId();
            $estimate->save();
        }

        self::createItems($estimate, $request);
        self::createAssigneUserGroup($estimate->id, $request->users_groups);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($estimate, $request);
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $estimate->addCustomFields($customFields);
        }

        return Estimate::with([
            'items.taxes',
            'user',
            'estimateTemplate',
            'taxes',
            'assingUsersGroups',
            'assingUser',
        ])->find($estimate->id);
    }

    public function updateEstimate($request)
    {
        $data = $request->except(['items', 'taxes']);

        $this->update($data);

        $this->items()->delete();
        $this->taxes()->delete();

        self::createItems($this, $request);

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($this, $request);
        }

        if ($request->customFields) {
            $this->updateCustomFields($request->customFields);
        }
        self::createAssigneUserGroup($request->id, $request->users_groups);

        return Estimate::with([
            'items.taxes',
            'user',
            'estimateTemplate',
            'taxes',
            'assingUsersGroups',
            'assingUser',
        ])
            ->find($this->id);
    }

    public static function createItems($estimate, $request)
    {
        $estimateItems = $request->items;

        foreach ($estimateItems as $estimateItem) {
            $estimateItem['company_id'] = $request->header('company');
            $item = $estimate->items()->create($estimateItem);

            if (array_key_exists('taxes', $estimateItem) && $estimateItem['taxes']) {
                foreach ($estimateItem['taxes'] as $tax) {
                    if (gettype($tax['amount']) !== "NULL") {
                        $tax['company_id'] = $request->header('company');
                        $item->taxes()->create($tax);
                    }
                }
            }
        }
    }

    public static function createAssigneUserGroup($estimate_id, $users_groups)
    {
        // use sync to assignee users and groups
        $estimate = Estimate::find($estimate_id);
        // array of user ids
        $ids = $estimate->assingUsersGroups->pluck('id')->toArray();
        // delete user group that not in array $users_groups
        foreach ($ids as $id) {
            // if not in array update to delete_at = now()
            if (! in_array($id, $users_groups)) {
                $estimate->assingUsersGroups()->updateExistingPivot($id, ['deleted_at' => DB::raw('NOW()')]);
            }
        }
        // add user group that not in array $users_groups
        foreach ($users_groups as $user_group) {
            if (! in_array($user_group, $ids)) {
                $estimate->assingUsersGroups()->attach([$user_group], ['company_id' => $estimate->company_id]);
            }
        }

    }

    public static function createTaxes($estimate, $request)
    {
        $estimateTaxes = $request->taxes;

        foreach ($estimateTaxes as $tax) {
            if (gettype($tax['amount']) !== "NULL") {
                $tax['company_id'] = $request->header('company');
                $estimate->taxes()->create($tax);
            }
        }
    }

    public function send($data)
    {
        $data['estimate'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = Company::find($this->company_id);
        $data['body'] = $this->getEmailBody($data['body']);
        $data['subject'] = $this->removeAttributesHtml($this->getEmailBody($data['subject']));
        // consultar email de usuario asociado al estimate
        $data['assigne_user'] = User::Find($data['estimate']['assigne_user_id']);
        $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        $data['PRIMARY_COLOR'] = $colorInvoice;

        \Mail::to($data['to'])->send(new SendEstimateMail($data));
        // enviar email a usuario asociado
        \Mail::to($data['assigne_user']->email)->send(new SendEstimateMail($data));

        if ($this->status == Estimate::STATUS_DRAFT) {
            $this->status = Estimate::STATUS_SENT;
            $this->save();
        }

        $bbcmail = CompanySetting::select('value', 'id')->where('option', 'estimate_bbc_email')->where('company_id', $this->company_id)->first();

        if ($bbcmail != null) {
            if ($bbcmail->value != "") {
                \Mail::to($bbcmail->value)->send(new SendEstimateMail($data));
            }

        }

        // envio de contactos
        $contacts = Contacts::select('email')->where('customer_id', $this->user_id)->where('allow_receive_emails', 1)->where('email_estimates', 1)->get();
        foreach ($contacts as $key => $email) {
            if ($email != null && $email != '') {
                // enviar email
                Mail::to($email)->send(new SendEstimateMail($data));
                // save emails logs
                // $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
            }
        }

        // enviar correo a usuarios
        $listUser = User::where('role', 'super admin')->where('email_estimates', 1)->where('company_id', $this->company_id)->get();

        foreach ($listUser as $user) {
            try {
                \Mail::to($user->email)->send(new SendEstimateMail($data));
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        return [
            'success' => true,
        ];
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

    public function getPDFData()
    {
        $taxTypes = [];
        $taxes = [];
        $labels = [];

        $taxTypes = [];
        $taxes = [];
        $labels = [];

        if ($this->tax_per_item === 'YES') {
            foreach ($this->items as $item) {
                foreach ($item->taxes as $tax) {
                    if (! in_array($tax->name, $taxTypes)) {
                        $taxTypes[] = $tax->name;
                        $labels[] = $tax->name.' ('.$tax->percent.'%)';
                    }
                }
            }

            foreach ($taxTypes as $taxType) {
                $total = 0;
                foreach ($this->items as $item) {
                    foreach ($item->taxes as $tax) {
                        if ($tax->name === $taxType) {
                            $total += $tax->amount;
                        }
                    }
                }
                $taxes[] = $total;
            }
        }

        $estimateTemplate = EstimateTemplate::find($this->estimate_template_id);

        $company = Company::find($this->company_id);
        $logo = $company->logo_base_64;

        $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';

        view()->share([
            'estimate' => $this,
            'logo' => $logo ?? null,
            'company_address' => $this->getCompanyAddress(),
            'shipping_address' => $this->getCustomerShippingAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'labels' => $labels,
            'taxes' => $taxes,
            'colorInvoice' => $colorInvoice,
            'Footer' => $this->getFooter(),
        ]);

        return PDF::loadView('app.pdf.estimate.'.$estimateTemplate->view);
    }

    public function getCompanyAddress()
    {
        $format = CompanySetting::getSetting('estimate_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerShippingAddress()
    {
        $format = CompanySetting::getSetting('estimate_shipping_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        $format = CompanySetting::getSetting('estimate_billing_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getNotes()
    {
        return $this->getFormattedString($this->notes);
    }

    public function getEmailBody($body)
    {
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());
        Log::info($body);

        $body = strtr($body, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getFooter()
    {
        $format = CompanySetting::getSetting('estimate_footer', $this->company_id);
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($format, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getExtraFields()
    {
        return [
            '{ESTIMATE_DATE}' => $this->formattedEstimateDate,
            '{ESTIMATE_EXPIRY_DATE}' => $this->formattedExpiryDate,
            '{ESTIMATE_NUMBER}' => $this->estimate_number,
            '{ESTIMATE_REF_NUMBER}' => $this->reference_number,
            '{ESTIMATE_LINK}' => '<a href="'.url('/customer/estimates/pdf/'.$this->unique_hash).'" class="button button-primary" >View Estimate</a>',
            '{APPROVAL_LINK}' => '<a href="'.url('/approve-estimates/'.$this->unique_hash).'" class="button button-primary" >Approve Estimate</a>',
        ];
    }
}
