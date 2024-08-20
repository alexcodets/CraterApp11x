<?php

namespace Crater\Http\Controllers\V1\Role;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Role;
use Crater\Models\RolPermisions;
use Crater\Models\User;
use Crater\Models\UserPermisions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "RoleController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($request->has('list')) {
            return Role::select(['id', 'name'])
                ->applyFilters($request->only([
                    'orderByField',
                    'orderBy'
                ]))->get();
        }

        $roles = Role::with('permissionss')
            ->applyFilters($request->only([
                'orderByField',
                'orderBy'
            ]))->allRoles($request->all, $request->name);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['roles' => $roles], "message" => "RoleController index"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/roles", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'roles' => $roles,
        ]);
    }

    public function assignRole(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "assignRole");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $users = User::select('id')->with('roles')->whereIn('id', collect($request->user_id)->pluck('id')->toArray())->get();

        $role_id = $request->role_id['id'];

        $users->each(function ($user) use ($role_id) {
            $user->roles()->sync($role_id);
        });

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['users' => $users], "message" => "RoleController users"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController users");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/user", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return $users;
    }

    public function permissions(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "permissions");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($request->roleId) {
            $permissions = Role::with('permissions')->where('id', $request->roleId)->first();
        } else {
            $permissions = Permission::all();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['permissions' => $permissions], "message" => "RoleController permissions"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController permissions");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/permissions", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return $permissions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $role = Role::create($request->all());

        if (! empty($request->permissionss)) {
            collect($request->permissionss)->each(function ($item) use ($role) {
                RolPermisions::create($item + [
                    'rol_id' => $role->id,
                    'company_id' => Auth::user()->company_id,
                    'creator_id' => Auth::user()->id
                ]);
            });
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['role' => $role], "message" => "RoleController role"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController role");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/role", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return $role;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $role = Role::with('permissionss')->find($id);
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['role' => $role], "message" => "RoleController role"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController role");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/role", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Crater\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Role $role)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['role' => $role], "message" => "RoleController role"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController role");
        /////////////////////////////////////////

        $role->update($request->all());

        // update permissions
        $role->permissionss()->delete();

        if (! empty($request->permissionss)) {

            //consulto user
            $listusers = User::where("role2", $role->name)->get();
            //Log::debug($listusers);

            foreach ($listusers as $user) {
                UserPermisions::where('user_id', $user->id)->delete();
            }
            collect($request->permissionss)->each(function ($item) use ($role, $listusers) {
                RolPermisions::create($item + [
                    'rol_id' => $role->id,
                    'company_id' => Auth::user()->company_id,
                    'creator_id' => Auth::user()->id
                ]);

                //Log::debug($item);

                foreach ($listusers as $user) {
                    UserPermisions::create([
                        'user_id' => $user->id,
                        'module' => $item['module'],
                        'access' => $item['access'],
                        'create' => $item['create'],
                        'read' => $item['read'],
                        'update' => $item['update'],
                        'delete' => $item['delete'],
                        'company_id' => $user->company_id,
                        'creator_id' => Auth::id(),
                    ]);
                }
            });
        }

        // if (!empty($request->permissions)) {
        //     $role->permissions()->sync(
        //         collect($request->permissions)->pluck('id')->toArray()
        //     );
        // }

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/role", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);


        return $role;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Role $role)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RoleController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ['role' => $role], "message" => "RoleController role"];

        LogsDev::finishLog($log, $res, $time, 'D', "RoleController role");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Roles", "Index", "admin/role", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        if ($role->delete()) {
            $role->permissionss()->delete();

            return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
        }
    }
}
