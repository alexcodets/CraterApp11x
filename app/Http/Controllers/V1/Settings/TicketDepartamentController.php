<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\CustomerTicket;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\TicketDepartament;
use Crater\Models\User;
use Illuminate\Http\Request;

class TicketDepartamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $departaments = TicketDepartament::applyFilters($request->only([
            'name', 'orderByField', 'orderBy', 'containsUsers'
        ]))
            ->whereCompany($request->header('company'))
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'departaments' => $departaments,
        ], "message" => "Departaments index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Packages index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Departaments", "List", "admin/tickets/departaments", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'departaments' => $departaments,
            'departamentTotalCount' => TicketDepartament::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "store");
        ////////////////


        $departament = TicketDepartament::createDepartament($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'departament' => $departament,
        ], "message" => "Departaments store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'departaments' => $departament,
            'success' => 'Departament save successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\TicketDepartament  $ticketDepartament
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "show");
        ////////////////

        /*    $departaments = TicketDepartament::where('ticket_departaments.id', $id); */
        $departaments = TicketDepartament::find($id);


        switch ($departaments['default_priority']) {
            case 'E':
                $departaments['default_priority'] = ['value' => 'E', 'text' => 'Emergency'];

                break;
            case 'C':
                $departaments['default_priority'] = ['value' => 'C', 'text' => 'Critical'];

                break;
            case 'H':
                $departaments['default_priority'] = ['value' => 'H', 'text' => 'High'];

                break;
            case 'M':
                $departaments['default_priority'] = ['value' => 'M', 'text' => 'Medium'];

                break;
            case 'L':
                $departaments['default_priority'] = ['value' => 'L', 'text' => 'Low'];

                break;
            default:
                break;
        }

        switch ($departaments['email_handling']) {
            case 'N':
                $departaments['email_handling'] = ['value' => 'N', 'text' => 'None'];

                break;
            case 'P':
                $departaments['email_handling'] = ['value' => 'P', 'text' => 'Piping'];

                break;
            case 'O':
                $departaments['email_handling'] = ['value' => 'O', 'text' => 'POP3'];

                break;
            case 'I':
                $departaments['email_handling'] = ['value' => 'I', 'text' => 'IMAP'];

                break;
            default:
                break;
        }

        switch ($departaments['status']) {
            case 'A':
                $departaments['status'] = ['value' => 'A', 'text' => 'Active'];

                break;
            case 'I':
                $departaments['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;
            default:
                break;
        }

        $departaments->load('users');

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
           'departaments' => $departaments,
        ], "message" => "Departaments show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'departaments' => $departaments
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\TicketDepartament  $ticketDepartament
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketDepartament $ticketDepartament)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\TicketDepartament  $ticketDepartament
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "update");
        ////////////////

        $departaments = TicketDepartament::find($id);
        /*  $data = $request; */
        $data['name'] = $request->name;
        $data['schedule_data'] = $request->schedule_data;
        $data['receive_tickets_emails'] = $request->receive_tickets_emails;
        $data['receive_mobile_tickets_emails'] = $request->receive_mobile_tickets_emails;
        $data['receive_tickets_messenger_notifications'] = $request->receive_tickets_messenger_notifications;
        $data['description'] = $request->description;
        $data['client_permission'] = $request->client_permission;
        $data['email'] = $request->email;
        $data['sender_override'] = $request->sender_override;
        $data['send_emails'] = $request->send_emails;
        $data['automatically_transition_admin'] = $request->automatically_transition_admin;
        $data['default_priority'] = $request->default_priority;
        $data['email_handling'] = $request->email_handling;
        $data['automatically_close'] = $request->automatically_close;
        $data['automatically_delete'] = $request->automatically_delete;
        $data['status'] = $request->status;
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;
        $departaments->updateUsers($request);
        $departaments ->update($data);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'departaments' => $departaments,
        ], "message" => "Departaments update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Departaments update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,



        return response()->json([
            'departaments' => $departaments,
            'success' => 'Departament save successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\TicketDepartament  $ticketDepartament
     * @return \Illuminate\Http\Response
     * TicketDepartament $ticketDepartament
     */
    public function destroy($id, Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "delete");

        // validar si existe Ticket con el departamento a borrar
        $exist = CustomerTicket::where('dep_id', $id)->first();
        if($exist) {
            $res = [
                "success" => false,
                "error" => "There is a customer ticket with this department."
            ];

            return response()->json($res);
        }

        TicketDepartament::deleteTicketDepartament($id);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Borrado de Ticket"
            ]
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de Ticket");

        return response()->json([
            'success' => true,
        ]);
    }

    public function delete(Request $request)
    {

        /* $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "delete");

        TicketDepartament::deleteTicketDepartament($request->ids);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Borrado de Ticket"
            ]
        ];

        //// Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "Borrado de Ticket");

        return response()->json([
            'success' => true,
        ]); */
    }

    public function getUsers(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TicketDepartamentController", "getUsers");

        $list = User::where("role", "<>", "customer")->get();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'list' => $list,
                    ],
                "message" => "Lista Usuarios"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Fin getUsers TicketDepartamentController");

        return response()->json([
            'list' => $list,
        ]);
    }

    public function getUsersByDepartment(TicketDepartament $department, Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $users = $department->users()->paginate($limit);

        return response()->json([
            'users' => $users
        ]);
    }

    public function getTicketsByDepartment(TicketDepartament $department, Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $tickets = $department->tickets()
            ->join('ticket_departaments', 'customer_tickets.dep_id', '=', 'ticket_departaments.id')
            ->join('users', 'customer_tickets.assigned_id', '=', 'users.id')
            ->orderBy($request->orderByField, $request->orderBy)
            ->select('customer_tickets.*', 'users.name as assigned', 'ticket_departaments.name as departament')
            ->paginate($limit);

        return response()->json([
            'tickets' => $tickets
        ]);
    }
}
