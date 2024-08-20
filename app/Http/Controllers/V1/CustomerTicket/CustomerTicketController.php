<?php

namespace Crater\Http\Controllers\V1\CustomerTicket;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomerTicketRequest;
use Crater\Http\Requests\DeleteTicketRequest;
use Crater\Models\CustomerPackage;
use Crater\Models\CustomerTicket;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Http\Request;

//use Log;

class CustomerTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Iniciar el registro de tiempo para el log de desarrollo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "index");

        // Establecer el límite de paginación, por defecto será 10 si no se especifica en la solicitud
        $limit = $request->input('limit', 10);

        // Inicializar la consulta base para CustomerTicket
        $query = CustomerTicket::query();

        // Aplicar filtros y joins solo si se proporciona customer_id y es mayor que 0
        if ($request->customer_id > 0) {
            $query->applyFilters($request->only(['orderByField', 'orderBy']))
                ->select('customer_tickets.*', 'users.name as assigned', 'ticket_departaments.name as departament')
                ->where('user_id', $request->customer_id)
                ->join('ticket_departaments', 'customer_tickets.dep_id', '=', 'ticket_departaments.id')
                ->join('users', 'customer_tickets.assigned_id', '=', 'users.id');

            // Contar el total de tickets para el customer_id dado
            $count = $query->count();
        } else {
            // Aplicar filtros y joins para la consulta general
            $query->applyFilters($request->only([
                'summary', 'note', 'user_id', 'dep_id', 'assigned_id',
                'from_date', 'to_date', 'priority', 'status', 'orderByField', 'orderBy',
            ]))
                ->select('customer_tickets.*', 'users.name as assigned', 'ticket_departaments.name as departament')
                ->join('ticket_departaments', 'customer_tickets.dep_id', '=', 'ticket_departaments.id')
                ->join('users', 'customer_tickets.assigned_id', '=', 'users.id');

            // Contar el total de tickets en general
            $count = CustomerTicket::count();
        }

        // Paginar los resultados de la consulta
        $customerTicket = $query->paginateData($limit);

        // Preparar la respuesta
        $response = [
            'success' => true,
            'response' => [
                'datamesage' => [
                    'customerTicket' => $customerTicket,
                    'customerTicketTotalCount' => $count,
                ],
                'message' => "Listado de tickets",
            ],
        ];

        // Finalizar el registro de log y guardar antes del return
        LogsDev::finishLog($log, $response, $time, 'D', "Listado de notas");

        // Retornar la respuesta en formato JSON
        return response()->json($response['response']['datamesage']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerTicketRequest $request)
    {
        // Validate "Ticket Number"
        if (! is_null($request["ticket_number_selected"])) {
            $ticket_number = CustomerTicket::where("ticket_number", $request["ticket_number_selected"])->first();
        } else {
            $ticket_number = CustomerTicket::where("ticket_number", $request["ticket_number"])->first();
        }

        if ($ticket_number) {
            return response()->json([
                'success' => false,
                'message' => "Ticket number already exists",
            ]);
        }
        //

        $time = microtime(true);
        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "store");
        // Create options groups
        $customerTicket = CustomerTicket::createCustomerTicket($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customerNote' => $customerTicket,
                    'success' => true,
                ],
                "message" => "Guardada la nota",
            ],
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerTicket' => $customerTicket,
        ], "message" => "CustomerTicket Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerTicket Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        $customerTicket->sendEmail($customerTicket);

        return response()->json([
            'customerTicket' => $customerTicket,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\CustomerTicket  $customerTicket
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $time = microtime(true);

        //Log::debug($id);
        $customerTicket = CustomerTicket::where('id', $id)
            ->select('*')->first();

        if ($customerTicket != null) {
            $customerTicket->load(
                'ticketsGroups',
                'ticketDepartament:id,name,description,email',
                'customer',
                'assigned:id,name,email,role,contact_name',
                'users:id,name,email,role,contact_name',
                'services',
                'pbxServices'
            );

            $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "show");

            //Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => ["datamesage" => [
                'customerTicket' => $customerTicket,
            ], "message" => "customerTicket show"]];
            LogsDev::finishLog($log, $res, $time, 'D', "customerTicket show");
            //Fin de registro de log, debe guardarse inmediatamente antes de un return,

            return response()->json([
                'customerTicket' => $customerTicket,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\CustomerTicket  $customerTicket
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerTicket $customerTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\CustomerTicket  $customerTicket
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerTicketRequest $request, CustomerTicket $customerTicket)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "update");

        // Update options groups
        $customerTicket = CustomerTicket::updateCustomerTicket($request, $customerTicket);

        $customerTicket->sendEmail($customerTicket, 'update');

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customerTicket' => $customerTicket,
                    'success' => true,
                ],
                "message" => "Actualizacion grupo de tickets",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion grupo de items");

        // Module log
        LogsModule::createLog(
            "CustomerTicket",
            "Update",
            "/admin/customers/id/id1/edit-ticket",
            $customerTicket->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "customerTicket: ".$customerTicket->name
        );

        return response()->json([
            'customerTicket' => $customerTicket,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\CustomerTicket  $customerTicket
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteTicketRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "destroy");
        //////////////////

        CustomerTicket::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'CustomerTicket deleted successfully',
        ], "message" => "CustomerTicket deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerTicket deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'CustomerTicket deleted successfully',
        ]);
    }

    public function getListUsers()
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* $time = microtime(true);
        $log = LogsDev::initLog("","", "D", "CustomerTicketController", "getListUsers"); */
        ///////////////////////////////////////

        $list = User::where("role2", "<>", "customer")->where("role2", "<>", "super admin")->get();
        /*  $list = User::all(); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        /* $res = ["success" => true, "response" => ["datamesage" => [
        'list' => $list,
        ], "message" => "Lista Usuarios"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider Index"); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'list' => $list,
        ]);
    }

    public function getListUsersCustomers()
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* $time = microtime(true);
        $log = LogsDev::initLog("","", "D", "CustomerTicketController", "getListUsers"); */
        ///////////////////////////////////////

        $list = User::where("role", "customer")->get();
        /*  $list = User::all(); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        /* $res = ["success" => true, "response" => ["datamesage" => [
        'list' => $list,
        ], "message" => "Lista Usuarios"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider Index"); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'list' => $list,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicesByCustomer(Request $request, $id)
    {
        try {
            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "getServicesByCustomer");

            $services = CustomerPackage::where('company_id', $request->header('company'))
                ->where('customer_id', $customer->id)
                ->where('status', 'A')
                ->with(['package', 'user'])
                ->get();

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'services' => $services,
                        'success' => true,
                    ],
                    "message" => "Lista de servicios asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de servicios asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Customer Tickets",
                "Get services",
                "admin/customer-ticket/{customer}/services",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'services' => $services,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getPbxServicesByCustomer(Request $request, $id)
    {
        try {

            $customer = User::findOrFail($id);
            $time = microtime(true);

            // Init Log
            $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "getPbxServicesByCustomer");

            $pbx_services = PbxServices::where('company_id', $request->header('company'))
                ->where('customer_id', $customer->id)
                ->where('status', 'A')
                ->with(['pbxPackage', 'user'])
                ->get();

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
                "Customers Tickets",
                "Get Pbx Services",
                "admin/customer-ticket/{customer}/pbx-services",
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getListByStatus(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerTicketController", "getListByStatus");

        $limit = $request->has('limit') ? $request->limit : 10;

        if ($request->input('status_oc')) {
            $customerTicket = CustomerTicket::applyFilters($request->only([
                'summary',
                'note',
                'user_id',
                'dep_id',
                'assigned_id',
                'from_date',
                'to_date',
                'priority',
                'status',
                'status_oc',
                'orderByField',
                'orderBy',
                'ticket_number',
                'search',
            ]))->select('customer_tickets.*', 'users.name as assigned', 'ticket_departaments.name as departament')
                ->join('ticket_departaments', 'customer_tickets.dep_id', '=', 'ticket_departaments.id')
                ->join('users', 'customer_tickets.assigned_id', '=', 'users.id')
                ->paginateData($limit);

            $count = CustomerTicket::count();

        } else {
            $res = [
                "success" => false,
                "response" => [
                    "message" => "status_oc parameter is not set",
                ],
            ];

            LogsDev::finishLog($log, $res, $time, 'D', "Listado de customer tickets");

            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'status_oc parameter is not set',
            ]);

        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerTicket' => $customerTicket,
            'customerTicketTotalCount' => $count,
        ], "message" => "Listado de tickets"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Listado de customer tickets");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        //Log::debug($customerTicket);

        return response()->json([
            'customerTicket' => $customerTicket,
        ]);
    }
}
