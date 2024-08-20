<?php

namespace Crater\Http\Controllers\V1\Users;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UserRequest;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerTicket;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\Role;
use Crater\Models\User;
use Crater\Models\UserPermisions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->input('limit', 10);

        $roles_name = Role::all()->pluck('name')->toarray();
        if (! is_null($request['roles'])) {
            $roles_name = Role::whereIn('id', $request['roles'])->get('name');
        }

        if ($request->has('list')) {
            return User::select(['id', 'name'])
                ->where('role', '!=', 'super admin')
                ->where('role', '!=', 'customer')
                ->get();
        }

        $users = User::where('role', '!=', 'customer')
            ->where('id', '<>', 1) // Se excluye al primer super admin
            ->applyFilters(
                $request->only([
                    'display_name',
                    'email',
                    'phone',
                    'roles',
                    'orderByField',
                    'orderBy',
                ])
            )
            ->whereIn('role2', $roles_name)
            ->latest()
            ->paginateData($limit);

        // Roles
        $roles = Role::all();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'users' => $users,
        ], "message" => "UsersController index"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController index");
        /////////////////////////////////////////
        // Logs por modulo
        LogsModule::createLog("Users", "List", "admin/users", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function getUserPermission(Request $request)
    {
        /*         $id = Auth::id();

        $user = User::find($id);

        $permissions = collect($user->givePermissionTo($request->slugs)->permissions);

        return $permissions->map(function($item){
        return [
        'slug' => $item['name'],
        'show' => true,
        ];
        }); */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $data['role'] = 'super admin';
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::id();
        $data['role2'] = $request->role['name'];

        $data['pbx_notification'] = $request->get("pbx_notification");
        $data['email_estimates'] = $request->get("email_estimates");
        $data['email_invoices'] = $request->get("email_invoices");
        $data['email_payments'] = $request->get("email_payments");
        $data['email_services'] = $request->get("email_services");
        $data['email_pbx_services'] = $request->get("email_pbx_services");
        $data['email_tickets'] = $request->get("email_tickets");
        $data['email_expenses'] = $request->get("email_expenses");

        //Log::debug($data);

        if ($data['pbx_notification']) {
            $data['pbx_notification'] = 1;
        }

        $user = User::create($data);

        if ($request->has('departament_groups')) {
            User::createItemGroups($user, $request);
        }
        if ($request->has('role') && $user) {
            User::assignPermissionsUser($user, $request->role);
        }

        $user->setSettings([
            'language' => CompanySetting::getSetting('language', $user->company_id),
        ]);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "UsersController index"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Users", "Create", "admin/users/create", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, User $user)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "UsersController show"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController show");
        ////////////////////////////////////////
        $user->load('itemGroups');

        //Log::debug($user);

        // Logs por modulo
        LogsModule::createLog("Users", "View", "admin/users/id/edit", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    public function showByid(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "UsersController show"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController show");
        ////////////////////////////////////////
        $user->load(['itemGroups:id,name', 'permissions']);

        // Logs por modulo
        LogsModule::createLog("Users", "View", "admin/users/id/edit", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

        return response()->json([
            'data' => $user,
            'success' => true,
        ]);
    }

    // updatePermissions
    public function updatePermissions(Request $request, $idUser)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "updatePermissions");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $user = User::find($idUser);
        Log::debug('user id', ['user_id' => $idUser]);
        // buscar todos los permisios de user
        $idsPermissions = UserPermisions::where('user_id', $idUser)->pluck('id')->toArray();
        $idsRequest = [];
        Log::debug("entro aqui 2", ['permissions' => $request->permissions]);
        foreach ($request->permissions as $permission) {
            //Log::debug($permission);

            if ($permission['access'] == false) {
                $permission['access'] = 0;
            }
            if ($permission['create'] == false) {
                $permission['create'] = 0;
            }
            if ($permission['read'] == false) {
                $permission['read'] = 0;
            }
            if ($permission['update'] == false) {
                $permission['update'] = 0;
            }
            if ($permission['delete'] == false) {
                $permission['delete'] = 0;
            }
            if ($permission['module'] != null) {
                if (isset($permission['id'])) {
                    $idsRequest[] = $permission['id'];

                    Log::debug("258", ['permission' => $permission]);

                    // si el id esta en $idsPermissions entonces actualizar
                    if (in_array($permission['id'], $idsPermissions)) {
                        Log::debug("262", ['permission' => $permission]);
                        $permissionUser = UserPermisions::where('user_id', $user->id)->where('id', $permission['id'])->first();
                        $permissionUser->update([
                            'module' => $permission['module']['value'],
                            'access' => $permission['access'],
                            'create' => $permission['create'],
                            'read' => $permission['read'],
                            'update' => $permission['update'],
                            'delete' => $permission['delete'],
                        ]);
                    }
                } else {
                    Log::debug("entro aqui 3");
                    UserPermisions::create([
                        'user_id' => $user->id,
                        'module' => $permission['module']['value'],
                        'access' => $permission['access'],
                        'create' => $permission['create'],
                        'read' => $permission['read'],
                        'update' => $permission['update'],
                        'delete' => $permission['delete'],
                        'company_id' => $user->company_id,
                        'creator_id' => Auth::id(),
                    ]);
                }
            }
        }

        // eliminar los permisos que no estan en el request
        $idsPermissions = array_diff($idsPermissions, $idsRequest);
        //Log::debug("idsPermissions");
        //Log::debug($idsPermissions);
        UserPermisions::destroy($idsPermissions);

        // $user->syncPermissions($request->permissions);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "UsersController updatePermissions"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController updatePermissions");
        ////////////////////////////////////////
        // Logs por modulo
        LogsModule::createLog("Users", "Update Permissions", "admin/users/id/edit", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    // etimatesAssignedUser
    public function estimatesAssignedUser(Request $request, $id)
    {
        Log::debug('filters estimates ', [$request->all()]);
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "show");
        $limit = $request->input('perPage', 10);


        $estimates = Estimate::with(['user:id,currency_id'])->where('assigne_user_id', $id)
            ->when($request->status, function ($query) use ($request) {
                if (isset($request->status)) {
                    return $query->where('status', $request->status);
                }
            })
            ->applyFilters(
                $request->only([
                    'display_name',
                    'email',
                    'phone',
                    'roles',
                    'orderByField',
                    'orderBy',
                ])
            )
            ->paginate($limit);

        $res = ["success" => true, "response" => [
            'estimates' => $estimates,
            'success' => true,
        ], "message" => "UsersController estimatesAssignedUser"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController estimatesAssignedUser");
        ////////////////////////////////////////
        LogsModule::createLog("Users", "View Estimates Assigned", "admin/users/id/edit", $id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$id);

        return response()->json([
            'estimates' => $estimates,
            'success' => true,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, User $user)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['User' => $user]);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* $user->role = $request->role['name']; */
        $user->role2 = $request->role['name'];
        $user->role = 'super admin';

        if ($request->get("pbx_notification")) {
            $user->pbx_notification = 1;
        } else {
            $user->pbx_notification = 1;
        }
        //Log::debug($request->input());
        $user->updateDepartament($request);
        $user->email_estimates = $request->get("email_estimates");
        $user->email_invoices = $request->get("email_invoices");
        $user->email_payments = $request->get("email_payments");
        $user->email_services = $request->get("email_services");
        $user->email_pbx_services = $request->get("email_pbx_services");
        $user->email_tickets = $request->get("email_tickets");
        //Log::debug($user);
        $user->save();
        $user->update($request->validated());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "UsersController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController update");
        ////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Users", "Update", "admin/users/id/edit", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        \Log::debug("delete");
        \Log::debug($request);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UsersController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($request->users) {
            foreach ($request->users as $user_id) {
                if ($this->existUser($user_id)) {
                    continue;
                }

                $user = User::find($user_id);

                // Logs por modulo
                LogsModule::createLog("Users", "delete", "admin/users", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User: ".$user->name);

                // Delete User
                User::destroy($request->users);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "UsersController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "UsersController update");
        ////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    public function existUser($creator_id)
    {
        $estimates = Estimate::where('creator_id', $creator_id)->count();
        $invoices = Invoice::where('creator_id', $creator_id)->count();
        $payments = Payment::where('creator_id', $creator_id)->count();
        $expenses = Expense::where('creator_id', $creator_id)->count();
        $tickets = CustomerTicket::where('creator_id', $creator_id)->count();

        if ($estimates == 0 && $invoices == 0 && $payments == 0 && $expenses == 0 && $tickets == 0) {
            return false;
        }

        return true;
    }

    public function getUserAdmin()
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* $time = microtime(true);
        $log = LogsDev::initLog("","", "D", "CustomerTicketController", "getListUsers"); */
        ///////////////////////////////////////

        $user = User::where('role', 'super admin')->first();
        /*  $list = User::all(); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        /* $res = ["success" => true, "response" => ["datamesage" => [
        'list' => $list,
        ], "message" => "Lista Usuarios"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider Index"); */
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'user' => $user,
        ]);
    }
}
