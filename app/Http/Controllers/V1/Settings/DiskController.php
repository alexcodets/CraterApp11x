<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DiskEnvironmentRequest;
use Crater\Models\FileDisk;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiskController extends Controller
{
    /**
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 5;
        $disks = FileDisk::applyFilters($request->all())
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'disks' => $disks,
        ], "message" => "DiskController"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Disk", "List", "admin/settings/file-disk", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'disks' => $disks,
        ]);
    }

    /**
     *
     * @param DiskEnvironmentRequest $request
     * @return JsonResponse
     */
    public function store(DiskEnvironmentRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if (! FileDisk::validateCredentials($request->credentials, $request->driver)) {
            return response()->json([
                'success' => false,
                'error' => 'invalid_credentials',
            ]);
        }

        $disk = FileDisk::createDisk($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
            'disk' => $disk,
        ], "message" => "DiskController store"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController store");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Disk", "Create", "admin/settings/file-disk", $disk->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Disk: ".$disk->name);

        return response()->json([
            'success' => true,
            'disk' => $disk,
        ]);
    }

    /**
     *
     * @param Request $request
     * @param \Crater\Models\FileDisk $file_disk
     * @return JsonResponse
     */
    public function update(FileDisk $disk, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['FileDisk' => $disk]);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $credentials = $request->credentials;
        $driver = $request->driver;

        if ($credentials && $driver && $disk->type !== 'SYSTEM') {
            if (! FileDisk::validateCredentials($credentials, $driver)) {

                //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
                $res = ["success" => true, "response" => ['success' => false, 'error' => 'invalid_credentials'], "message" => "DiskController update"];
                LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
                /////////////////////////////////////////

                return response()->json([
                    'success' => false,
                    'error' => 'invalid_credentials',
                ]);
            }

            $disk->updateDisk($request);
        } elseif ($request->set_as_default) {
            $disk->setAsDefaultDisk();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
            'disk' => $disk,
        ], "message" => "DiskController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Disk", "Update", "admin/settings/file-disk", $disk->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Disk: ".$disk->name);

        return response()->json([
            'success' => true,
            'disk' => $disk,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show($disk, Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Disk' => $disk]);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        // Logs por modulo

        $diskData = [];
        switch ($disk) {
            case 'local':
                $diskData = [
                    'root' => config('filesystems.disks.local.root'),
                ];

                break;

            case 's3':
                $diskData = [
                    'key' => '',
                    'secret' => '',
                    'region' => '',
                    'bucket' => '',
                    'root' => '',
                ];

                break;

            case 'doSpaces':
                $diskData = [
                    'key' => '',
                    'secret' => '',
                    'region' => '',
                    'bucket' => '',
                    'endpoint' => '',
                    'root' => '',
                ];

                break;

            case 'dropbox':
                $diskData = [
                    'token' => '',
                    'key' => '',
                    'secret' => '',
                    'app' => '',
                    'root' => '',
                ];

                break;
        }

        $data = array_merge($diskData);

        LogsModule::createLog("Disk", "Create", "admin/settings/file-disk", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Disk get drivers");

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $data, "message" => "DiskController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
        /////////////////////////////////////////

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\FileDisk  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileDisk $disk, Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(FileDisk' => $disk]);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($disk->setAsDefault() && $disk->type === 'SYSTEM') {

            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [
                'success' => false,
            ], "message" => "DiskController update"];
            LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
            /////////////////////////////////////////

            return response()->json([
                'success' => false,
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("Disk", "delete", "admin/settings/file-disk", $disk->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Disk: ".$disk->name);

        $disk->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "DiskController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     *
     * @return JsonResponse
     */
    public function getDiskDrivers(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "DiskController", "getDiskDrivers");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $drivers = [
            [
                'name' => 'Local',
                'value' => 'local',
            ],
            [
                'name' => 'Amazon S3',
                'value' => 's3',
            ],
            [
                'name' => 'Digital Ocean Spaces',
                'value' => 'doSpaces',
            ],
            [
                'name' => 'Dropbox',
                'value' => 'dropbox',
            ],
        ];

        $default = config('filesystems.default');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'drivers' => $drivers,
            'default' => $default,
        ], "message" => "DiskController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "DiskController update");
        /////////////////////////////////////////

        return response()->json([
            'drivers' => $drivers,
            'default' => $default,
        ]);
    }
}
