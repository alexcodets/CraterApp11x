<?php

namespace Crater\Http\Controllers\V1\Contacts;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ContactsRequest;
use Crater\Models\Contacts;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $customer_id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ContactsController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        //Log::debug($customer_id);

        $contacts = Contacts::select('*')->applyFilters($request->only([
            'orderByField',
            'orderBy',
        ]))->where('customer_id', $customer_id)->paginate($limit);
        $contactsTotalCount = Contacts::select('*')->where('customer_id', $customer_id)->count();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $response = [
            "success" => true,
            "status" => 200,
            'contacts' => $contacts,
            "message" => "List of Contacts",
            'totalCount' => $contactsTotalCount,
        ];
        // Logs por modulo
        LogsModule::createLog("Contacts", "index", "admin/customer/contacts", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        LogsDev::finishLog($log, $response, $time, 'D', "Contacts index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json($response, $response['status']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ContactsController", "store");

        $data = $request->validated();

        if($request->input('password') != null) {
            $data['password'] = Crypt::encrypt($request->input('password'));
            $data['repeat_password'] = $data['password'];
        }

        try {
            $contact = Contacts::create($data);
            $response = [
                'status' => 200,
                'response' => 'Contact saved correctly',
                'success' => true,
                'contact' => $contact,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            $response = [
                'status' => 406,
                'response' => 'Error saving Contacts',
                'message' => $th->getMessage(),
                'success' => false,
            ];
        }

        // Logs por modulo
        LogsModule::createLog("Contacts", "store", "admin/customer/contacts", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        LogsDev::finishLog($log, $response, $time, 'D', "Contacts store");

        return response()->json($response, $response['status']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ContactsController", "index");

        $contact = Contacts::find($id);

        //$pass = $contact->password;
        //Log::debug(Crypt::decrypt($pass));
        // Crypt::decryptString():

        if ($contact) {
            $response = [
                'status' => 200,
                'response' => 'Contact correctly',
                'success' => true,
                'contact' => $contact,
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error geting Contact',
                'success' => false,
            ];
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        LogsDev::finishLog($log, $response, $time, 'D', "ContactsController show");

        // Logs por modulo
        LogsModule::createLog("ContactsController", "show", "admin/customer/contacts", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ContactsController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        /* //Log::debug('request---');
        //Log::debug($request); */

        if ($request->has('id')) {
            $contact = Contacts::find($request->id);
        }

        $contact->allow_receive_emails = $request->allow_receive_emails;
        $contact->name = $request->name;
        $contact->last_name = $request->last_name;
        $contact->phone = $request->phone;
        $contact->position = $request->position;
        $contact->email = $request->email;

        $contact->log_in_credentials = $request->log_in_credentials;
        $contact->password = Crypt::encrypt($request->password);
        $contact->repeat_password = $contact->password;
        $contact->invoices = $request->invoices;
        $contact->estimates = $request->estimates;
        $contact->payments = $request->payments;
        $contact->tickets = $request->tickets;
        $contact->payments_accounts = $request->payments_accounts;
        $contact->reports = $request->reports;
        $contact->email_estimates = $request->email_estimates;
        $contact->email_invoices = $request->email_invoices;
        $contact->email_payments = $request->email_payments;
        $contact->email_services = $request->email_services;
        $contact->email_pbx_services = $request->email_pbx_services;
        $contact->email_tickets = $request->email_tickets;
        $contact->type = $request->type;

        $updated = $contact->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'response' => 'Contact update correctly',
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error updating Contact',
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "Contacts update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Contacts", "Update", "admin/customer/address", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ContactsController", "destroy");
        //////////////////

        //Log::debug($request->input('id'));
        try {
            Contacts::destroy($request->input('id'));
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = [
                "success" => true,
                "message" => "Contact deleted successfully",
            ];

        } catch (\Throwable $th) {
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = [
                "success" => false,
                "message" => $th->getMessage(),
            ];
        }

        LogsDev::finishLog($log, $res, $time, 'D', $res['message']);
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'message' => $res['message'],
            'success' => $res['success'],
        ]);
    }
}
