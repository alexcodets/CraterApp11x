<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\Http\Controllers\V1\Customer\CustomersController;
use Crater\Notifications\MailResetPasswordNotification;
use Crater\Traits\HasCustomFieldsTrait;
use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;
use Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use Notifiable;
    use InteractsWithMedia;
    use HasCustomFieldsTrait;
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customcode',
        'avalara_location_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'company_id',
        'password',
        'facebook_id',
        'currency_id',
        'google_id',
        'github_id',
        'role',
        'role2',
        'group_id',
        'phone',
        'company_name',
        'contact_name',
        'prepaid_option',
        'auto_debit',
        'email_low_balance_notification',
        'auto_replenish_amount',
        'minimun_balance',
        'website',
        'enable_portal',
        'creator_id',
        'customer_type',
        'avalara_bool',
        'avalara_type',
        'customer_username',
        'authentication',
        'username_status',
        'password_encrypted',
        'security_pin',
        'status_customer',
        'language',
        'timezone',
        'auto_suspension',
        'add_shipping_addres',
        'status_payment',
        'sale_type',
        'lfln',
        'incorporated',
        'type_vat_regime',
        'great_contributor',
        'self_retaining',
        'vat_withholding_agent',
        'simple_tax_regime',
        'not_applicable_others',
        'pbx_notification',
        'email_estimates',
        'email_invoices',
        'email_payments',
        'email_services',
        'email_pbx_services',
        'email_tickets',
        'email_expenses',
        'avatar',
        'lead_id',
        'verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'currency',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'avatar',
        'formattedMakePayment',
        'formattedAddCredit',
    ];

    /**
     * Find the user instance for the given username.
     *
     * @param string $username
     * @return \Crater\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->first();
    }

    public function setPasswordAttribute($value)
    {
        if ($value != null) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function isSuperAdminOrAdmin()
    {
        return ($this->role == 'super admin') || ($this->role == 'admin');
    }

    public function isCustomer()
    {
        return ($this->role == 'customer');
    }

    public function isCommonuser()
    {
        return ($this->role == 'super admin') || ($this->role == 'admin') || ($this->role == 'customer');
    }

    public static function login($request)
    {
        $remember = $request->remember;
        $email = $request->email;
        $password = $request->password;

        return (\Auth::attempt(['email' => $email, 'password' => $password], $remember));
    }

    public function getFormattedCreatedAtAttribute($value): string
    {
        $dateFormat = Cache::remember("carbon_date_format_{$this->company_id}", 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getFormattedMakePaymentAttribute()
    {
        // Obtén el registro de CompanySetting que coincida con la company_id actual
        $setting = CompanySetting::where('company_id', $this->company_id)
            ->where('option', 'enable_make_customer')
            ->first();

        // \Log::debug($setting );
        if (! $setting) {
            // Si no se encuentra el registro, retorna 'NO'
            //\Log::debug('No se encontró el registro en CompanySetting');
            return 'NO';
        }

        // Verifica el valor de la columna 'value'
        if ($setting->value == 0 || $setting->value == false) {
            // Si el valor es 0 o false, retorna 'no'
            //\Log::debug('El valor en CompanySetting es 0 o false');
            return 'NO';
        } elseif ($setting->value == 1 || $setting->value == true) {
            // Si el valor es 1 o true, retorna 'YES'
            //\Log::debug('El valor en CompanySetting es 1 o true');
            return 'YES';
        }
    }

    public function getFormattedAddCreditAttribute()
    {
        // Obtén el registro de CompanySetting que coincida con la company_id actual
        $setting = CompanySetting::where('company_id', $this->company_id)
            ->where('option', 'enable_credit_customer')
            ->first();

        if (! $setting) {
            // Si no se encuentra el registro, retorna 'NO'
            return 'NO';
        }

        // Verifica el valor de la columna 'value'
        if ($setting->value == 0 || $setting->value === false) {
            // Si el valor es 0 o false, retorna 'no'
            return 'NO';
        } elseif ($setting->value == 1 || $setting->value === true) {
            // Si el valor es 1 o true, retorna 'YES'
            return 'YES';
        }
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Packages::class, 'customer_packages', 'customer_id', 'package_id')
            ->withPivot('id', 'status');
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Crater\Models\User::class, 'creator_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', Address::BILLING_TYPE);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', Address::SHIPPING_TYPE);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoicesBetwhenDates(string $start, string $end): HasMany
    {
        //format = 'Y-m-d'
        return $this->hasMany(Invoice::class) //->select('id', 'total', 'invoice_date', 'user_id', 'invoice_number', 'user_id')
            ->whereBetween(
                'invoice_date',
                [$start, $end]
            );

    }

    public function invoicesdebt()
    {
        $total = 0;
        $invoices = $this->hasMany(Invoice::class)->where('status', '!="', 'DRAFT')->where('status', "!=", "SAVE_DRAFT");

        return $total;
    }

    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_id');
    }

    public function companySettings(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class);
    }

    /**
     * Main Location For the Invoice
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(AvalaraLocation::class, 'avalara_location_id');
    }

    /**
     * All The Location from the user
     * @return HasMany
     */
    public function locations(): HasMany
    {
        return $this->hasMany(AvalaraLocation::class);
    }

    public function exemptions(): HasMany
    {
        return $this->hasMany(AvalaraExemption::class);
    }

    /**
     * Main Location For the Invoice
     * @return HasMany
     */
    public function pbxServices(): HasMany
    {
        return $this->hasMany(PbxServices::class, 'customer_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermisions::class);
    }

    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermisions::class);
    }

    public function pushNotificationLogs(): HasMany
    {
        return $this->hasMany(PushNotificationsLogs::class, 'customer_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contacts::class, 'customer_id');
    }

    /**
     * Override the mail body for reset password notification mail.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('email', 'LIKE', '%'.$term.'%')
                    ->orWhere('status_customer', 'LIKE', '%'.$term.'%')
                    ->orWhere('phone', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereContactName($query, $contactName)
    {
        return $query->where('contact_name', 'LIKE', '%'.$contactName.'%');
    }

    public function scopeWhereDisplayName($query, $displayName)
    {
        return $query->where('name', 'LIKE', '%'.$displayName.'%');
    }

    public function scopeWherePhone($query, $phone)
    {
        return $query->where('phone', 'LIKE', '%'.$phone.'%');
    }

    public function scopewhereStatusCustomer($query, $status_customer)
    {
        $query->where('status_customer', $status_customer);
    }

    public function scopewhereStatusPaymentCustomer($query, $status_payment)
    {

        $query->where('status_payment',  $status_payment);
    }

    public function scopeWhereEmail($query, $email)
    {
        return $query->where('email', 'LIKE', '%'.$email.'%');
    }

    public function scopeCustomer($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeCustomerActive($query)
    {
        return $query->where('status_customer', 'A');
    }

    public function scopePrepaid($query)
    {
        return $query->where('status_payment', 'prepaid');
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

        if ($filters->get('contact_name')) {
            $query->whereContactName($filters->get('contact_name'));
        }

        if ($filters->get('display_name')) {
            $query->whereDisplayName($filters->get('display_name'));
        }

        if ($filters->get('email')) {
            $query->whereEmail($filters->get('email'));
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }

        if ($filters->get('phone')) {
            $query->wherePhone($filters->get('phone'));
        }

        if ($filters->get('status_customer')) {
            $query->whereStatusCustomer($filters->get('status_customer'));
        }

        if ($filters->get('status_payment')) {
            $query->whereStatusPaymentCustomer($filters->get('status_payment'));

        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }

    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('users.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->orWhere('users.id', $customer_id);
    }

    public function scopeApplyInvoiceFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->invoicesBetween($start, $end);
        }

        if ($filters->get('users_id')) {
            $filters->put('users_id', explode(',', $filters->get('users_id')));
            $query->whereIn('creator_id', $filters->get('users_id'));
        }
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        $query->whereHas('invoices', function ($query) use ($start, $end) {
            $query->whereBetween(
                'invoice_date',
                [$start->format('Y-m-d'), $end->format('Y-m-d')]
            );
        });
    }

    public static function deleteCustomers($ids)
    {
        foreach ($ids as $id) {

            $customer = self::find($id);

            if ($customer != null) {

                $conts = CustomerPackage::where('customer_id', $customer->id)->where('status', 'A')->get()->count();
                $contpbx = PbxServices::where('customer_id', $customer->id)->where('status', 'A')->get()->count();
                $continvo = Invoice::where('user_id', $customer->id)->where('status', '<>', 'COMPLETED')->whereNUll("deleted_at")->get()->count();

                if ($conts == 0 && $contpbx == 0 && $continvo == 0) {

                    $customer['status_customer'] = 'I';
                    $customer->save();

                    LogsModule::createLog("customers", "delete", "admin/customers/delete", $id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Customer: ".$customer->name);

                    if ($customer->estimates()->exists()) {
                        $customer->estimates()->delete();
                    }

                    if ($customer->invoices()->exists()) {
                        $customer->invoices()->delete();
                    }

                    if ($customer->payments()->exists()) {
                        $customer->payments()->delete();
                    }

                    if ($customer->addresses()->exists()) {
                        $customer->addresses()->delete();
                    }

                    // Delete Services
                    $service_ids = CustomerPackage::where('customer_id', $customer->id)
                        ->where('status', '<>', 'C')
                        ->pluck('id')
                        ->toArray();

                    CustomerPackage::deleteCustomerPackage($service_ids);

                    // Delete Pbx Services
                    $pbx_services = PbxServices::where('customer_id', $customer->id)
                        ->where('status', '<>', 'C')
                        ->get();

                    foreach ($pbx_services as $pbx_service) {
                        PbxServices::deleteAssociations($pbx_service);
                        PbxServices::deleteExtensionsAndDids($pbx_service);
                        $pbx_service->status = 'C';
                        $pbx_service->save();
                        $pbx_service->delete();
                    }
                    //Log::debug("customer borrado");
                    //Log::debug($customer);

                    $customer->delete();

                    return [
                        'type' => 'success',
                        'message' => 'Customer deleted successfully',
                    ];

                } else {
                    //Log::debug("customer  sin borrado");
                    //Log::debug($customer);

                    return [
                        'type' => 'warning',
                        'message' => 'This client cannot be deleted, it has active services or pending invoices',
                    ];
                }
            }

            return [
                'type' => 'error',
                'message' => 'Customer not found',
            ];
        }

        return false;
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->getMedia('admin_avatar')->first();

        if ($avatar) {
            return asset($avatar->getUrl());
        }

        return 0;
    }

    public function getAvatarBase64Attribute(): ?string
    {
        $avatar = $this->getMedia('avatar')->first();

        if ($avatar) {
            $filePath = $avatar->getPath();
            $type = File::mimeType($filePath);

            return 'data:'.$type.';base64,'.base64_encode(file_get_contents($filePath));
        }

        return null;
    }

    public static function createCustomer($request)
    {
        //Log::debug($request->input());
        $data = $request->only([
            'customcode',
            'name',
            'first_name',
            'last_name',
            'email',
            'status_payment',
            'sale_type',
            'phone',
            'company_name',
            'contact_name',
            'website',
            'enable_portal',
            'customer_type',
            'avalara_bool',
            'avalara_type',
            'customer_username',
            'authentication',
            'username_status',
            'password_encrypted',
            'status_customer',
            'language',
            'timezone',
            'auto_suspension',
            'security_pin',
            'add_shipping_addres',
            'minimun_balance',
            'prepaid_option',
            'auto_debit',
            'email_low_balance_notification',
            'auto_replenish_amount',
            'type_vat_regime',
            'great_contributor',
            'self_retaining',
            'vat_withholding_agent',
            'simple_tax_regime',
            'not_applicable_others',
            'billing',
            'incorporated',
            'lead_id',
        ]);

        $data['creator_id'] = Auth::id();
        $data['company_id'] = $request->header('company');
        $data['role'] = 'customer';
        if ($request->password) {
            $data['password'] = $request->password;
        }
        $data['password_encrypted'] = Crypt::encryptString($request->password);

        //Crea el codigo aleatorio por cliente

        /* $dt = Carbon::now();
        $first = rand(100,999);
        $last = rand(100,999);
        $customcode= strval($first)."".strval($dt->year)."".strval($dt->month)."".strval($dt->day)."".strval($last);
         */
        $customer = User::create($data);

        if ($data['lead_id'] != null) {
            Lead::where('id', $data['lead_id'])->update([
                'status' => 'C',
            ]);
        }

        $customer['currency_id'] = $request->currency_id;
        $customer->save();

        $setting['key'] = 'language';
        $setting['value'] = 'en';
        $setting['user_id'] = $customer['id'];

        if ($request->authentication) {
            $user_setting = UserSetting::create($setting);
            $user_setting->save();
        }

        /*
        $preflix = User::find(Auth::id());

        //se agrega el customcode al customer
        if (empty($preflix->customcode)) {
        $customcode = 'cust-0000' . $customer->id;
        } else {
        $customcode = $preflix->customcode . '-0000' . $customer->id;
        }

        $customerUser = User::find($customer->id);
        $customerUser->customcode = $customcode;
        $customerUser->save();
         */

        $avalara_addresses_id = null;
        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                $address['company_id'] = $customer->company_id;
                $addressCreated = $customer->addresses()->create($address);
                if ($addressCreated->type == 'billing') {
                    $avalara_addresses_id = $addressCreated->id;
                }
            }
        }
        if ($request->avalara_bool) {
            $country = Country::find($request->billing['country_id'])->code;
            $state = State::find($request->billing['state_id'])->code;
            $dataAvalaraLocation = [
                'customer_id' => $customer->id,
                'company_id' => $customer->company_id,
                'city' => $request->billing['city'],
                'locality' => $request->billing['city'],
                'county' => $request->billing['county'],
                'pcd' => $request->billing['pcode'],
                'state' => $state,
                'user_id' => $customer->id,
                'zip' => $request->billing['zip'],
                'country' => $country,
                'address' => $request->billing['address_street_1'],
                'addresses_id' => $avalara_addresses_id,
                'type' => $request->billing['pcode'] != "" ? 0 : null,
            ];
            $resultAvalaraLocation = AvalaraLocation::create($dataAvalaraLocation);
            $customer->avalara_location_id = $resultAvalaraLocation->id;
            $customer->save();
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->addCustomFields($customFields);
        }

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
    }

    public static function getNextCustomerNumber($prefix)
    {
        // Get the last created order
        /*$lastOrder = User::where('customcode', 'LIKE', $value . '-%')
        ->withTrashed()
        ->orderBy('customcode', 'desc')
        ->first();

        if (!$lastOrder) {
        $number = 0;
        } else {
        $number = explode("-", $lastOrder->customcode);
        $number = $number[1];
        }

        return sprintf('%06d', intval($number) + 1);*/

        // Obtener el último número de factura en la secuencia especificada
        $lastNumber = User::where('customcode', 'LIKE', "$prefix-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(customcode, '-', -1) AS UNSIGNED) DESC")
            ->value('customcode');

        \Log::debug($lastNumber);
        // Obtener el número siguiente único
        $nextNumber = $lastNumber ? intval(substr($lastNumber, strlen($prefix) + 1)) + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Verificar la unicidad del número generado
        $existingInvoice = User::where('customcode', "$prefix-$formattedNumber")->exists();

        // Si el número generado ya existe, buscar el siguiente número único
        while ($existingInvoice) {
            $nextNumber++;
            $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $existingInvoice = User::where('customcode', "$prefix-$formattedNumber")->exists();
        }

        return $formattedNumber;
    }

    public function getCustomerNumAttribute()
    {
        $position = $this->strposX($this->customcode, "-", 1) + 1;

        return substr($this->customcode, $position);
    }

    public function getCustomerPrefixAttribute()
    {
        $prefix = explode("-", $this->customcode)[0];

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

    // assignRole
    public function assignPermissionsUser($user, $role)
    {
        // buscar el rol
        $role = Role::with('permissionss')->find($role['id']);
        foreach ($role->permissionss as $permission) {
            UserPermisions::create([
                'user_id' => $user->id,
                'module' => $permission->module,
                'access' => $permission->access,
                'create' => $permission->create,
                'read' => $permission->read,
                'update' => $permission->update,
                'delete' => $permission->delete,
                'company_id' => $permission->company_id,
                'creator_id' => Auth::id(),
            ]);
        }
    }

    public static function updateCustomer($request, $customer)
    {
        // desencripta la contraseña old
        $passwordOld = Crypt::decryptString($customer->password_encrypted);
        $username_statusOld = $customer->username_status;

        $data = $request->only([
            'customcode',
            'avalara_bool',
            'avalara_type',
            'auto_suspension',
            'name',
            'first_name',
            'last_name',
            'currency_id',
            'status_payment',
            'sale_type',
            'email',
            'phone',
            'company_name',
            'contact_name',
            'bscl',
            'svcl',
            'fclt',
            'reg',
            'prepaid_option',
            'auto_debit',
            'email_low_balance_notification',
            'auto_replenish_amount',
            'website',
            'enable_portal',
            'customer_type',
            'customer_username',
            'authentication',
            'username_status',
            'password_encrypted',
            'status_customer',
            'security_pin',
            'add_shipping_addres',
            'minimun_balance',
            'timezone',
            'lfln',
            'type_vat_regime',
            'great_contributor',
            'self_retaining',
            'vat_withholding_agent',
            'simple_tax_regime',
            'not_applicable_others',
            'billing',
            'incorporated',
            'language',
        ]);

        $data['role'] = 'customer';
        if ($request->has('password')) {
            $customer->password = $request->password;
        }
        $data['password_encrypted'] = Crypt::encryptString($request->password);

        $customer->update($data);

        // $customer->addresses()->delete();
        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                // validar si tiene id
                if (isset($address['id'])) {
                    $addressUpdate = Address::find($address['id']);
                    $addressUpdate->update($address);
                } else {
                    $customer->addresses()->create($address);
                }
            }
        }

        $customFields = $request->customFields;

        if ($customFields) {
            $customer->updateCustomFields($customFields);
        }

        // Enviar email cuando cambie el password y username_status
        if ($passwordOld != $request->password ||
            $username_statusOld != $request->username_status) {
            CustomersController::email_low($customer, "customer_account_registration", "New Account registration");
        }

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return $customer;
    }

    public function setSettings($settings)
    {
        foreach ($settings as $key => $value) {
            $this->settings()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'key' => $key,
                    'value' => $value,
                ]
            );
        }
    }

    public function getSettings($settings)
    {
        $settings = $this->settings()->whereIn('key', $settings)->get();
        $companySettings = [];

        foreach ($settings as $setting) {
            $companySettings[$setting->key] = $setting->value;
        }

        return $companySettings;
    }

    public function itemGroups(): BelongsToMany
    {
        return $this->belongsToMany(TicketDepartament::class, 'departament_users', 'user_id', 'dep_group_id')
            ->whereNull('departament_users.deleted_at')
            ->withTimestamps();
    }

    public static function createItemGroups($item, $request)
    {
        foreach ($request->departament_groups as $group) {
            $item->itemGroups()
                ->attach(
                    $group['dep_group_id'],
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public function updateDepartament($request)
    {
        // Eliminar los grupos asociados
        self::deleteDepartamentGroups($this);

        if ($request->has('item_groups')) {
            // Asociar nuevos grupos
            self::createItemGroups($this, $request);
        }
    }

    public static function deleteDepartamentGroups($dep)
    {
        foreach ($dep->itemGroups as $group) {
            $dep->itemGroups()->updateExistingPivot($group->id, ['deleted_at' => Carbon::now()]);
        }
    }

    public function customerConfigs(): HasOne
    {
        return $this->hasOne(CustomerConfig::class, 'customer_id');
    }

    public function customerConfig(): HasOne
    {
        return $this->hasOne(CustomerConfig::class, 'customer_id');
    }

    public function paymentAccount(): HasOne
    {
        return $this->hasOne(PaymentAccount::class, 'client_id');
    }

    public function notesTicket(): HasMany
    {
        return $this->hasMany(NoteTicket::class, 'user_id');
    }

    public function table(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function auxVaultSettings(): HasMany
    {
        return $this->hasMany(AuxVaultSetting::class, 'user_id');
    }
}
