<?php

namespace Crater\Http\Controllers\V1\Customer;

use Auth;
use Carbon\Carbon;
use Crater\Avalara\Apis\AvalaraApi;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests;
use Crater\Mail\CustomerCreation;
//// Models
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\CustomerPackage;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Packages;
use Crater\Models\PbxServices;
use Crater\Models\Prefix;
use Crater\Models\User;
use Crater\Models\UserSetting;
//Traits
use Crater\Traits\SendEmailsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;
use Throwable;

class CustomersController extends Controller
{
    use SendEmailsTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $limit = $request->input('limit', 10);

        // $query = User::query();
        // Crear una consulta base
        $baseQuery = User::with('creator')
            ->customer()
            ->applyFilters($request->only([
                'search',
                'contact_name',
                'display_name',
                'phone',
                'customer_id',
                'status_customer',
                'status_payment',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->select('users.*', DB::raw('sum(invoices.due_amount) as due_amount'))
            ->groupBy('users.id')
            ->leftJoin('invoices', function ($join) {
                $join->on('users.id', '=', 'invoices.user_id')
                    ->whereNull('invoices.deleted_at');
            });

        // Modificar la consulta base según la condición
        if ($request->avalara_bool == 'true') {
            $customers = $baseQuery->where('avalara_bool', true)
                ->paginateData($limit);
        } else {
            $customers = $baseQuery->with('packages')
                ->paginateData($limit);
        }

        // Logs por modulo
        LogsModule::createLog("customers", "List", "admin/customers", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'customers' => $customers,
            'customerTotalCount' => User::whereRole('customer')->count(),
        ]);
    }

    // Método para obtener una lista de clientes con paginación y filtros aplicados.
    public function indexselectcustomer(Request $request)
    {
        // Establece un límite predeterminado para la paginación o utiliza el proporcionado en la solicitud.
        $limit = $request->input('limit', 10);

        // Construye la consulta base utilizando el modelo User y aplicando los filtros necesarios.
        $baseQuery = User::customer() // Aplica un alcance local para filtrar solo los usuarios que son clientes.
            ->applyFilters($request->only([ // Aplica filtros basados en los parámetros de la solicitud.
                'search',
                'contact_name',
                'display_name',
                'phone',
                'customer_id',
                'status_customer',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company')) // Filtra por compañía usando un encabezado de la solicitud.
            ->select('users.id', 'users.name'); // Selecciona todos los campos de la tabla 'users'.

        // Obtiene los clientes con paginación.
        $customers = $baseQuery->paginateData($limit);

        // Registra la acción en los logs del módulo.
        LogsModule::createLog(
            "customers", // Nombre del módulo.
            "List", // Acción realizada.
            "admin/customers", // Ruta asociada a la acción.
            0, // ID del registro afectado, 0 si no aplica.
            Auth::user()->name, // Nombre del usuario autenticado.
            Auth::user()->email, // Email del usuario autenticado.
            Auth::user()->role, // Rol del usuario autenticado.
            Auth::user()->company_id// ID de la compañía del usuario autenticado.
        );

        // Devuelve la respuesta en formato JSON con la lista de clientes.
        return response()->json([
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\CustomerRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "store");
        ///////////////////////////////////////

        $customer = User::createCustomer($request);

        $sendemail = $request->get('send_after_created_email');
        $config = CustomerConfig::where('customer_id', 0)->first();

        if ($config) {
            $Nuevorequest = new Request();
            $config['customer_id'] = $customer->id;
            $vari = $config->toArray();
            $Nuevorequest->request->add($vari);
            $nuevo = new CustomerConfigController();
            $nuevo->customerConfig($Nuevorequest);
        }

        // Logs por modulo
        LogsModule::createLog("customers", "Create", "admin/customers/create", $customer->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Customer: ".$customer->name);

        //correo de credenciales
        try {
            if ($customer->authentication == 1) {
                $this->email_low($customer, "customer_account_registration", "customer_account_registration_subject");
            }

        } catch (\Throwable $th) {
            \Log::debug($th);
        }

        //correo de bienvenida

        try {

            if ($sendemail == 1) {
                $this->email_low($customer, "customer_customer_creation", "customer_customer_creation_subject");
            }
        } catch (\Throwable $th) {
            \Log::debug($th);
        }

        $usersetting = new UserSetting();
        $usersetting->key = "language";
        $usersetting->value = "en";
        $usersetting->user_id = $customer->id;
        $usersetting->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'success' => true,
        ], "message" => "Guardado un customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado un customer");
        /////////////////////////////////////////

        return response()->json([
            'customer' => $customer,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, User $customer)
    {
        $serviciospnx = PbxServices::where("customer_id", $customer->id)->where(function ($query) {
            $query->where('status', 'A')
                ->orWhere('status', 'P')
                ->orWhere('status', 'S');
        })->get()->count();
        $servicios = CustomerPackage::where("customer_id", $customer->id)->where(function ($query) {
            $query->where('status', 'A')
                ->orWhere('status', 'P')
                ->orWhere('status', 'S');
        })->get()->count();

        /* //Log::debug( "prepaid");
        //Log::debug( $serviciospnx);
         */
        // Log::debug( $servicios);
        $tiene = 0;
        if ($serviciospnx > 0 || $servicios > 0) {
            $tiene = 1;
        }
        $customer->servicescount = $tiene;
        //Log::debug($customer);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['customer' => $customer]);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "show");
        ///////////////////////////////////////

        $customer->load([
            'billingAddress.country',
            'shippingAddress.country',
            'billingAddress.state',
            'shippingAddress.state',
            'fields.customField',
            'creator',
            'packages',
        ]);
        Log::debug($customer);
        if ($customer['password_encrypted']) {
            /* //Log::debug($customer['password_encrypted']);
            //Log::debug($customer->password_encrypted); */
            $customer['password_encrypted'] = Crypt::decryptString($customer->password_encrypted);
        }

        $customer['company'] = Company::select('name')->first();

        switch ($customer['status_customer']) {
            case 'A':
                $customer['status_customer'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $customer['status_customer'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            case 'F':
                $customer['status_customer'] = ['value' => 'F', 'text' => 'Archive'];

                break;

            default:
                break;
        }

        $currency = $customer->currency;

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'currency' => $currency,
        ], "message" => "Guardado un customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado un customer");
        /////////////////////////////////////////

        // Logs por modulo
        // LogsModule::createLog("customers", "View", "admin/customers/id/view", $customer->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Customer: " . $customer->name);

        return response()->json([
            'customer' => $customer,
            'currency' => $currency,
            'customerNumber' => $customer->getCustomerNumAttribute(),
            'customerPrefix' => $customer->getCustomerPrefixAttribute(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\User $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\CustomerRequest $request, User $customer)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['customer' => $customer]);
        $log = LogsDev::initLog($request2, "", "D", "CustomersController", "update");
        ///////////////////////////////////////
        \Log::debug('request customer controller', ['request' => $request->all(), 'customer' => $customer]);
        $customer = User::updateCustomer($request, $customer);

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'success' => true,
        ], "message" => "Actualizacion un customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion un customer");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("customers", "Update", "admin/customers/id/edit", $customer->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Customer: ".$customer->name);

        try {
            if ($customer->authentication == 1 && $customer->sendedcredentials == 0) {
                $this->email_low($customer, "customer_account_registration", "customer_account_registration_subject");
                $customer->sendedcredentials = 1;
                $customer->save();

            }
        } catch (\Throwable $th) {
            \Log::debug($th);
        }

        return response()->json([
            'customer' => $customer,
            'success' => true,
        ]);
    }

    public function setPrefixGeneral(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "setPrefixGeneral");
        ///////////////////////////////////////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "setPrefixGeneral"]];
        LogsDev::finishLog($log, $res, $time, 'D', "setPrefixGeneral");
        /////////////////////////////////////////

        // Logs por modulo

        return response()->json([
            'success' => true,
            'customcode' => $user->customcode,
        ]);
    }

    public function fetchPrefix(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "fetchPrefix");
        ///////////////////////////////////////

        $user = User::find(Auth::id());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "fetchPrefix"]];
        LogsDev::finishLog($log, $res, $time, 'D', "fetchPrefix");
        /////////////////////////////////////////

        // Logs por modulo

        return response()->json([
            'success' => true,
            'customcode' => $user->customcode,
        ]);
    }

    public function setPrefix(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "setPrefix");
        ///////////////////////////////////////

        $query = User::query();

        $customers = $query->where('role', 'customer')
            ->where('creator_id', Auth::id())
            ->get();

        $prefix = $request->customers_prefix;
        $busq = CompanySetting::where('option', '=', 'customer_prefix')->where('company_id', '=', $request->header('company'))->first();

        if (! $busq) {
            $neww = new CompanySetting();
            $neww->option = "customer_prefix";
            $neww->value = $prefix;
            $neww->company_id = $request->header('company');
            $neww->save();
        } else {
            $busq->value = $prefix;
            $busq->save();
        }

        if (! empty($prefix) && $request->customers_prefix_general == 'true') {
            $customers->each(function ($item) use ($prefix) {
                $up = User::find($item['id']);
                $up->customcode = $prefix.'-0000'.$item['id'];
                $up->save();
            });
        } else {
            $customers->each(function ($item) use ($prefix) {
                $up = User::find($item['id']);
                if (is_null($up->customcode)) {
                    $up->customcode = 'cust-0000'.$item['id'];
                    $up->save();
                }
            });
        }

        if (empty($prefix) && $request->customers_prefix_general == 'true') {
            $customers->each(function ($item) use ($prefix) {
                $up = User::find($item['id']);
                $up->customcode = 'cust-0000'.$item['id'];
                $up->save();
            });
        }

        $user = User::find(Auth::id());
        $oldcustomcode = $user->customcode;
        $user->customcode = $request->customers_prefix;
        $user->save();

        $PrefixIndex = Prefix::where('name', $oldcustomcode)->exists();

        if (! is_null($request->customers_prefix)) {

            if ($PrefixIndex) {
                Prefix::where(['name' => $oldcustomcode])->update([
                    'name' => $request->customers_prefix,
                ]);
            } else {
                Prefix::create(['name' => $request->customers_prefix, 'from' => 'customer']);
            }
        }

        self::setServicePrefix($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "setPrefix"]];
        LogsDev::finishLog($log, $res, $time, 'D', "setPrefix");
        /////////////////////////////////////////

        // Logs por modulo

        return response()->json([
            'success' => true,
        ]);
    }

    public function setServicePrefix($request)
    {
        $settings = ['service_prefix' => $request->service_prefix];
        CompanySetting::setSettings($settings, $request->header('company'));

        if ($request->general_service_prefix) {
            $services = CustomerPackage::where('company_id', $request->header('company'))->get();

            foreach ($services as $service) {
                $service->code = $request->service_prefix.'-0000'.$service->id;
                $service->save();
            }
        }
    }

    /**
     * Remove a list of Customers along side all their resources (ie. Estimates, Invoices, Payments and Addresses)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "delete");
        ///////////////////////////////////////

        $delete_response = User::deleteCustomers($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Borrado de customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de customer");
        /////////////////////////////////////////

        // Logs por modulo

        return response()->json($delete_response);
    }

    /**
     * Agrega un paquete a un cliente
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePackage(Request $request)
    {
        $parameters = $request->parameters;
        $c_Pck = $request->packageCustomer;

        $customer = User::findOrFail($parameters['customer_id']);
        $package = Packages::findOrFail($parameters['package']['value']);
        $setting = CompanySetting::where('option', 'service_prefix')
            ->whereCompany($request->header('company'))
            ->first();

        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "CustomersController", "savePackage");

        $currentDate = Carbon::now();
        //Log::debug($currentDate);
        //Log::debug($parameters['date_begin']);
        $discount_deadline = Carbon::parse($parameters['date_begin']);
        //Log::debug($discount_deadline);

        $resulttime = $discount_deadline->lte($currentDate);
        //Log::debug($resulttime);

        if ($resulttime) {

            $date_prev = $currentDate;
            $param_getRenewalDate = $currentDate;
            $parameters['status']['value'] = "A";

        } else {

            $date_prev = $parameters['date_begin'];
            $param_getRenewalDate = $parameters['date_begin'];
            $parameters['status']['value'] = "P";

        }

        $customer_pkg = CustomerPackage::create([
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'creator_id' => Auth::id(),
            'company_id' => $request->header('company'),
            'tax_by' => $parameters['tax_type']['value'],
            'allow_discount' => $parameters['allow_discount'],
            'discount_by' => $parameters['discount_type']['value'],
            'discount_type' => $c_Pck['discount_type'],
            'discount' => $c_Pck['discount'],
            'discount_val' => $c_Pck['discount_val'],
            'sub_total' => $c_Pck['sub_total'],
            'total' => $c_Pck['total'],
            'tax' => $c_Pck['tax'],
            'status' => $parameters['status']['value'],
            'term' => $parameters['term']['value'],
            'activation_date' => $parameters['date_begin'],
            'date_prev' => $date_prev,
            'service_auto_suspension' => $parameters['service_auto_suspension'],
            'addresses_id' => isset($parameters['address']['id']) ? $parameters['address']['id'] : null,
        ]);

        if ($parameters['status']['value'] == 'A' && empty($parameters['date_begin'])) {
            $customer_pkg->activation_date = Carbon::parse($customer_pkg->created_at)->format('Y-m-d');
        }

        if (! empty($customer_pkg->activation_date)) {
            $customer_pkg->renewal_date = $customer_pkg->getRenewalDate($param_getRenewalDate);
        }

        $customer_pkg->date_prev = $date_prev;
        $customer_pkg->code = $setting ? $setting->value.'-0000'.$customer_pkg->id : 'PBX-0000'.$customer_pkg->id;
        $customer_pkg->save();

        /*-------------- Items for package -------------*/
        foreach ($c_Pck['items'] as $item) {
            $item['company_id'] = $request->header('company');
            $item['creator_id'] = Auth::id();
            $c_pck_item = $customer_pkg->items()->create($item);

            // Taxes for item
            if (array_key_exists('taxes', $item) && $item['taxes']) {
                foreach ($item['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');
                    $tax['creator_id'] = Auth::id();
                    if (gettype($tax['amount']) !== "NULL") {
                        $c_pck_item->taxes()->create($tax);
                    }
                }
            }
        }

        //Log::debug("taaxes");

        /*-------------- Taxes for package --------------*/
        if (array_key_exists('taxes', $c_Pck) && $c_Pck['taxes']) {
            foreach ($c_Pck['taxes'] as $tax) {
                $tax['company_id'] = $request->header('company');
                $tax['creator_id'] = Auth::id();
                if (gettype($tax['amount']) !== "NULL") {
                    $customer_pkg->taxes()->create($tax);
                }
            }
        }

        //Log::debug("discount");
        /*------------- Discount dates ------------------*/
        if ($parameters['allow_discount'] && (! empty($parameters['discount_start_date']) || ! empty($parameters['discount_time_units']))) {
            $customer_pkg->discounts()->create([
                'creator_id' => Auth::id(),
                'company_id' => $request->header('company'),
                'discount_type' => $c_Pck['discount_type'],
                'discount' => $c_Pck['discount'],
                'discount_val' => $c_Pck['discount_val'],
                'term_type' => $parameters['discount_term_type']['value'],
                'start_date' => $parameters['discount_start_date'],
                'end_date' => $parameters['discount_end_date'],
                'time_unit_number' => $parameters['discount_time_units'],
                'term' => $parameters['discount_term']['value'],
            ]);
        }

        //Log::debug("correo");
        try {
            //Log::debug($customer_pkg);
            $this->sendEmailServices($customer->id, $request->header('company'), 'create', 'Service Created', $customer_pkg);
        } catch (Throwable $e) {
            //return response()->json(['message' => $e->getMessage()]);
            //Log::debug('--- catch send email services----');
            //Log::debug($e);
        }

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customer_package' => $customer_pkg,
                    'success' => true,
                ],
                "message" => "Guardado paquete asociado a cliente",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado paquete asociado a cliente");

        // Module log
        LogsModule::createLog(
            "Customers",
            "Save Package",
            "admin/customers/:id/add-package",
            $customer_pkg->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "CustomerPackage: ".$customer_pkg->id
        );

        //Log::debug("Return");
        return response()->json([
            'customer_package' => $customer_pkg,
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackages(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomersController", "getPackages");

            $limit = $request->input('limit', 10);

            $packagesList = CustomerPackage::where('company_id', $request->header('company'))
                ->where('customer_id', $customer->id)
                ->where('status', $request->status)
                ->with(['package', 'user'])
                ->orderBy($request->orderByField, $request->orderBy)
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'packagesList' => $packagesList,
                        'success' => true,
                    ],
                    "message" => "Lista de paquetes asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de paquetes asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Customers",
                "Get Packages",
                "admin/customers/:id/packages",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'packagesList' => $packagesList,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getInvoices(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomersController", "getInvoices");

            $limit = $request->has('limit') ? $request->limit : 10;

            $invoicesList = Invoice::where('company_id', $request->header('company'))
                ->where('user_id', $customer->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query
                        ->when($request->status == 'PENDING', function ($q) {
                            return $q->where('due_date', '>', Carbon::now());
                        })
                        ->when($request->status != 'PENDING', function ($q) use ($request) {
                            if ($request->status == 'ARCHIVED') {
                                return $q->onlyTrashed();
                            } else {
                                return $q->where('status', $request->status);
                            }
                        });
                })
                ->with('user')
                ->orderBy($request->orderByField, $request->orderBy)
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'invoicesList' => $invoicesList,
                        'success' => true,
                    ],
                    "message" => "Lista de facturas asociadas a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de facturas asociadas a un cliente");

            // Module log
            LogsModule::createLog(
                "Customers",
                "Get Invoices",
                "admin/customers/:id/invoices",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'invoicesList' => $invoicesList,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getEstimates(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomersController", "getEstimates");

            $limit = $request->has('limit') ? $request->limit : 10;

            $estimatesList = Estimate::where('company_id', $request->header('company'))
                ->where('user_id', $customer->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query
                        ->when($request->status == 'PENDING', function ($q) {
                            return $q->where('estimate_date', '>', Carbon::now());
                        })
                        ->when($request->status != 'PENDING', function ($q) use ($request) {
                            return $q->where('status', $request->status);
                        });
                })
                ->with('user')
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'estimatesList' => $estimatesList,
                        'success' => true,
                    ],
                    "message" => "Lista de estimados asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de estimados asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Customers",
                "getEstimates",
                "admin/customers/:id/estimates",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'estimatesList' => $estimatesList,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    //Get Services

    public function getServices(Request $request)
    {
        $customer = User::findOrFail($request["customer_id"]);
        $limit = $request->has('limit') ? $request->limit : 10;

        $services = CustomerPackage::where('customer_packages.customer_id', $customer->id)
            ->where('customer_packages.company_id', $request->header('company'))
            ->where('customer_packages.status', $request["status"])
            ->join('packages', 'customer_packages.package_id', '=', 'packages.id')
            ->with(['user'])
            ->select('customer_packages.*', 'customer_packages.id as customer_package_id')
            ->applyFilters($request->only([
                'orderByField',
                'orderBy',
            ]))
            ->paginateData($limit);

        return response()->json([
            'services' => $services,
            'success' => true,
        ]);
    }

    public function getPbxServices(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init Log
            $log = LogsDev::initLog($request, "", "D", "CustomersController", "getPbxServices");

            $limit = $request->input('limit', 10);

            $pbx_services = PbxServices::where('company_id', $request->header('company'))
                ->where('customer_id', $request->customer_id)
                ->where('status', $request->status)
                ->with(['pbxPackage', 'user', 'pbxServiceExtensions', 'pbxServiceDids'])
                ->orderBy($request->orderByField, $request->orderBy)
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        "pbxServices" => $pbx_services,
                        "success" => true,
                    ],
                    "message" => "Servicios PBX asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, "D", "Fin Servicios PBX asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Customers",
                "Get Pbx Services",
                "admin/customers/:id/pbx_services",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                "pbxServices" => $pbx_services,
                "success" => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function customerUsernameAvailable(Request $request, $username)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "customerUsernameAvailable");
        ///////////////////////////////////////

        $username = User::where('customer_username', $username)->first();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Busqueda de un customer username"]];
        LogsDev::finishLog($log, $res, $time, 'D', "customerUsernameAvailable");
        /////////////////////////////////////////

        return response()->json([
            'username' => $username,
            'success' => true,
        ]);
    }

    public function sendpassword(Request $request, $id)
    {

        $customer = User::findOrFail($id);
        if ($customer != null) {

            try {
                $this->email_low($customer, "customer_account_registration", "customer_account_registration_subject");
                $customer->password = $request->password;
                $customer->password_encrypted = Crypt::encryptString($request->password);
                $customer->save();

                return response()->json([
                    'message' => "Credentials sended",
                    'success' => true,
                ]);
            } catch (Throwable $e) {
                return response()->json(['message' => $e->getMessage()]);
            }
        } else {
            return response()->json([
                'message' => "Customer not found",
                'success' => false,
            ]);
        }

    }

    /*
     * $customer
     * $direction = email body
     * $title = email subject
     */
    public function email_low($customer, $direccion = false, $title = false)
    {
        // $superadmin = User::where("role", "super admin")->first();
        $add = Address::whereNULL("user_id")
            ->join("countries", "countries.id", "=", "addresses.country_id")
            ->join("states", "states.id", "=", "addresses.state_id")
            ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
            ->first();

        if ($add == null) {
            $add = Address::first()
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                ->first();
        }

        $message = CompanySetting::where("company_id", $customer->company_id)->where("option", $direccion)->first();
        $newTitle = CompanySetting::where("company_id", $customer->company_id)->where("option", $title)->first();

        $title = "Account Customer";

        if ($newTitle != null) {
            $title = strip_tags($newTitle->value);
        }

        $company = Company::where("id", $customer->company_id)->first();

        if ($customer != null && $message != null && $add != null && $direccion != false && $title != false) {
            $newMessage = $message->value;
            $array = [];
            $array["PRIMARY_CONTACT_NAME"] = $customer->name;
            $array["PRIMARY_COLOR"] = self::getPrimaryColor($customer->company_id);
            $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
            $array["CONTACT_EMAIL"] = $customer->email;
            $array["CONTACT_USERNAME"] = $customer->customer_username;
            if ($array["CONTACT_USERNAME"] == "" || $array["CONTACT_USERNAME"] == null) {
                $array["CONTACT_USERNAME"] = $array["CONTACT_EMAIL"];
            }
            $array["CONTACT_PHONE"] = $customer->phone;
            $array["CONTACT_WEBSITE"] = $customer->website;
            $array["CONTACT_ROLE"] = $customer->role;
            $array["COMPANY_NAME"] = $company->name;
            $array["CONTACT_BALANCE"] = number_format($customer->balance, 2, '.', '');
            $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"] = $customer->email;
            $array["CONTACT_AUTO_REPLENISH_AMOUNT"] = $company->auto_replenish_amount;
            $array["CONTACT_CUSTOM_CODE"] = $company->customcode;
            $array["CONTACT_STATUS_CUSTOMER"] = $company->status_payment;
            $array["CONTACT_MINIMUN_BALANCE"] = $company->minimun_balance;
            $array["COMPANY_COUNTRY"] = $add->country;
            $array["COMPANY_STATE"] = $add->state;
            $array["STATE_CODE"] = $add->state_code;
            $array["COMPANY_CITY"] = $add->city;
            $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
            $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
            $array["COMPANY_PHONE"] = $add->phone;
            $array["COMPANY_ZIP_CODE"] = $add->zip;
            $array["CONTACT_PASSWORD"] = ! is_null($customer->password_encrypted) ? Crypt::decryptString($customer->password_encrypted) : null;
            $array["CUSTOMER_LOGIN"] = url("").'/login';

            // EMAIL BODY
            $newMessage = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newMessage);
            $newMessage = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newMessage);
            $newMessage = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newMessage);
            $newMessage = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newMessage);
            $newMessage = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $newMessage);
            $newMessage = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newMessage);
            $newMessage = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newMessage);
            $newMessage = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $newMessage);
            $newMessage = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $newMessage);
            $newMessage = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $newMessage);
            $newMessage = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $newMessage);
            $newMessage = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $newMessage);
            $newMessage = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $newMessage);
            $newMessage = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $newMessage);
            $newMessage = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newMessage);
            $newMessage = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newMessage);
            $newMessage = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newMessage);
            $newMessage = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newMessage);
            $newMessage = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newMessage);
            $newMessage = str_replace("{CONTACT_PASSWORD}", $array["CONTACT_PASSWORD"], $newMessage);
            $newMessage = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newMessage);
            $newMessage = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newMessage);
            $newMessage = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newMessage);
            $newMessage = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newMessage);
            $newMessage = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newMessage);

            // EMAIL SUBJECT
            $title = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $title);
            $title = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $title);
            $title = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $title);
            $title = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $title);
            $title = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $title);
            $title = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $title);
            $title = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $title);
            $title = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $title);
            $title = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $title);
            $title = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $title);
            $title = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $title);
            $title = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $title);
            $title = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $title);
            $title = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $title);
            $title = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $title);
            $title = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $title);
            $title = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $title);
            $title = str_replace("{STATE_CODE}", $array["STATE_CODE"], $title);
            $title = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $title);
            $title = str_replace("{CONTACT_PASSWORD}", $array["CONTACT_PASSWORD"], $title);
            $title = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $title);
            $title = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $title);
            $title = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $title);
            $title = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $title);
            //Log::debug($newMessage);
            $data['company'] = $company;
            $data['PRIMARY_COLOR'] = self::getPrimaryColor($customer->company_id);

            try {
                // $title = $this->removeAttributesHtml($title);
                Mail::to($array["CONTACT_EMAIL"])->send(new CustomerCreation($title, $newMessage, $data));
                // save emails logs
                $mailable_id = $message->id;
                // $customer_id = \Auth::user()->id;
                //Log::debug('llegó-------');  frivero@careonecomm.com
                // $emailTrait = new SendEmailsTrait;
                // $this->saveEmailLog($customer->email, $subject = '', $newMessage, $mailable_id, $company->id, $customer->id);

            } catch (Exception $ex) {
                // jump to this part
                // if an exception occurred
            }
        } else {
        }
    }

    public function billingValidation(Request $request)
    {
        $api = new AvalaraApi();

        if ($api) {
            $check = $api->locationPCode([
                "CountryISO" => $request->country,
                "State" => $request->state,
                "City" => $request->city,
                "ZipCode" => $request->zip_code,
                "BestMatch" => true,
            ]);

            return response()->json(['check' => $check]);
        }
    }

    public function customersDisabled(Request $request)
    {
        //Log::debug(Auth::user());  Se obtiene el usuario en curso
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $query = User::query();

        if ($request->avalara_bool == 'true') {
            $customers = User::onlyTrashed()->with('creator')
                ->customer()
                ->applyFilters($request->only([
                    'search',
                    'contact_name',
                    'display_name',
                    'phone',
                    'customer_id',
                    'status_customer',
                    'orderByField',
                    'orderBy',
                ]))
                ->whereCompany($request->header('company'))
                ->select(
                    'users.*',
                    DB::raw('sum(invoices.due_amount) as due_amount')
                )
                ->groupBy('users.id')
                ->leftJoin('invoices', 'users.id', '=', 'invoices.user_id')
                ->where('avalara_bool', true)
                ->paginateData($limit);
        } else {

            $customers = User::onlyTrashed()->with('creator')
                ->with('packages')
                ->customer()
                ->applyFilters($request->only([
                    'search',
                    'contact_name',
                    'display_name',
                    'phone',
                    'customer_id',
                    'status_customer',
                    'orderByField',
                    'orderBy',
                ]))
                ->whereCompany($request->header('company'))
                ->select(
                    'users.*',
                    DB::raw('sum(invoices.due_amount) as due_amount')
                )
                ->groupBy('users.id')
                ->leftJoin('invoices', 'users.id', '=', 'invoices.user_id')
                ->paginateData($limit);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customers' => $customers,
            'customerTotalCount' => User::onlyTrashed()->whereRole('customer')->count(),
        ], "message" => "Listado customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Fin de index customer");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("customers", "List", "admin/customers", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'customers' => $customers,
            'customerTotalCount' => User::onlyTrashed()->whereRole('customer')->count(),
        ]);
    }

    public function restoreCustomer(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "delete");
        ///////////////////////////////////////

        User::withTrashed()->find($request->id)->restore();
        Address::withTrashed()->where('user_id', $request->id)->restore();

        $user = User::find($request->id);
        if ($user != null) {
            $user->status_customer = "A";
            $user->save();

        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "restore customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Restore customer");
        /////////////////////////////////////////

        // Logs por modulo

        return response()->json([
            'success' => true,
            'type' => 'success',
        ]);
    }

    public function showCustomerWithoutLogin(Request $request, $customer)
    {
        // return "Aqui va el show del customer";

        $customer = User::where('id', $customer)->first();

        $serviciospnx = PbxServices::where("customer_id", $customer->id)->where(function ($query) {
            $query->where('status', 'A')
                ->orWhere('status', 'P')
                ->orWhere('status', 'S');
        })->get()->count();
        $servicios = CustomerPackage::where("customer_id", $customer->id)->where(function ($query) {
            $query->where('status', 'A')
                ->orWhere('status', 'P')
                ->orWhere('status', 'S');
        })->get()->count();

        /* //Log::debug( "prepaid");
        //Log::debug( $serviciospnx);
        //Log::debug( $servicios); */
        $tiene = 0;
        if ($serviciospnx > 0 || $servicios > 0) {
            $tiene = 1;
        }
        $customer->servicescount = $tiene;
        //Log::debug($customer);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['customer' => $customer]);
        $log = LogsDev::initLog($request, "", "D", "CustomersController", "show");
        ///////////////////////////////////////

        $customer->load([
            'billingAddress.country',
            'shippingAddress.country',
            'billingAddress.state',
            'shippingAddress.state',
            'fields.customField',
            'creator',
            'packages',
        ]);

        if ($customer['password_encrypted']) {
            /* //Log::debug($customer['password_encrypted']);
            //Log::debug($customer->password_encrypted); */
            $customer['password_encrypted'] = Crypt::decryptString($customer->password_encrypted);
        }

        $customer['company'] = Company::select('name')->first();

        switch ($customer['status_customer']) {
            case 'A':
                $customer['status_customer'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $customer['status_customer'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            case 'F':
                $customer['status_customer'] = ['value' => 'F', 'text' => 'Archive'];

                break;

            default:
                break;
        }

        $currency = $customer->currency;

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'currency' => $currency,
        ], "message" => "Guardado un customer"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Guardado un customer");
        /////////////////////////////////////////

        // Logs por modulo
        // LogsModule::createLog("customers", "View", "admin/customers/id/view", $customer->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Customer: " . $customer->name);

        return response()->json([
            'customer' => $customer,
            'currency' => $currency,
        ]);
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

    public function getCustomers()
    {
        return response()->json([
            'customers' => User::with('creator')
                ->customer()
                ->get(),
        ]);
    }
}
