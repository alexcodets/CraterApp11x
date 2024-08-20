<?php

namespace Crater\Http\Controllers\V1\Package;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeletePackageGroupRequest;
use Crater\Http\Requests\PackageGroupRequest;
use Crater\Models\LogsDev;
use Crater\Models\PackageGroup;
use Crater\Models\PackageGroups;
use Crater\Models\Packages;
use Illuminate\Http\Request;

class PackageGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackageGroupController", "index");
        ////////////////

        $limit = $request->input('limit', 10);

        $package_groups = PackageGroups::applyFilters($request->only([
                'search',
                'name',
                'description',
                'orderByField',
                'orderBy',
            ]))
            ->whereStatus('A')
            ->select('*')
            ->latest()
            ->paginateData($limit);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'groups' => $package_groups,
            'groupTotalCount' => PackageGroups::whereStatus('A')->count(),
        ], "message" => "Package group Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Package group Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'groups' => $package_groups,
            'groupTotalCount' => PackageGroups::whereStatus('A')->count(),
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
    public function store(PackageGroupRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackageGroupController", "store");
        ////////////////

        $package_groups = PackageGroups::createPackageGroup($request);

        if(count($request->packageLeft) > 0) {
            $add_packages = PackageGroups::addPackages(collect($request->packageLeft), $package_groups->id);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'groups' => $package_groups,
        ], "message" => "Package group store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Package group store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'groups' => $package_groups
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PackageGroup  $packageGroup
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackageGroupController", "show");
        ////////////////

        $package_groups = PackageGroups::find($id);
        $packages = PackageGroups::showPackageGroup($id);

        if ($package_groups['allow_upgrades'] === 0) {
            $package_groups['allow_upgrades'] = false;
        } elseif ($package_groups['allow_upgrades'] === 1) {
            $package_groups['allow_upgrades'] = true;
        }

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'groups' => $package_groups,
        ], "message" => "Package group show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Package group show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'groups' => $package_groups,
            'packages' => $packages
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\PackageGroup  $packageGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(PackageGroup $packageGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PackageGroup  $packageGroup
     * @return \Illuminate\Http\Response
     */
    public function update(PackageGroupRequest $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackageGroupController", "update");
        ////////////////

        $package_groups = PackageGroups::find($id);

        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $package_groups->update($data);

        // Eliminar grupo de impuestos asociados a un tipo de impuesto
        $PackageGroups = PackageGroup::where('package_groups_id', $package_groups['id'])->get();
        foreach ($PackageGroups as $PackageGroup) {
            $PackageGroup->deleted_at = Carbon::now();
            $PackageGroup->save();
        }

        // Guardar grupo de impuestos asociados a un tipo de impuesto
        if (count($request->packageLeft) > 0) {
            foreach ($request->packageLeft as $package) {
                $PackageGroup = new PackageGroup();
                $PackageGroup->packages_id = $package['id'];
                $PackageGroup->package_groups_id = $package_groups['id'];
                $PackageGroup->save();
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'groups' => $package_groups,
        ], "message" => "Package group update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Package group update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'groups' => $package_groups,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PackageGroup  $packageGroup
     * @return \Illuminate\Http\Response
     */
    public function delete(DeletePackageGroupRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackageGroupController", "destroy");
        //////////////////

        PackageGroups::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Package group deleted successfully',
        ], "message" => "Package group deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Package group deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Package group deleted successfully',
        ]);
    }

    public function packages()
    {
        return Packages::all();
    }
}
