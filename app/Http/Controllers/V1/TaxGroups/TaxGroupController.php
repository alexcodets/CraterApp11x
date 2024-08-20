<?php

namespace Crater\Http\Controllers\V1\TaxGroups;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteTaxGroupRequest;
use Crater\Http\Requests\TaxGroupRequest;
use Crater\Models\Country;
use Crater\Models\LogsDev;
use Crater\Models\PbxPackages;
use Crater\Models\State;
use Crater\Models\Tax;
use Crater\Models\TaxGroup;
use Crater\Models\TaxGroups;
use Crater\Models\TaxType;
use DB;
use Illuminate\Http\Request;
use Log;

class TaxGroupController extends Controller
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

        // $log = LogsDev::initLog($request, "", "D", "TaxGroupController", "index");
        ////////////////

        $limit = $request->has('limit') ? $request->limit : 10;

        if ($request['orderByField'] == 'country') {
            $request['orderByField'] = 'countries.name';
        }

        if ($request['orderByField'] == 'state') {
            $request['orderByField'] = 'states.name';
        }

        $tax_groups = TaxGroups::applyFilters($request->only([
            'country',
            'state',
            'search',
            'name',
            'description',
            'orderByField',
            'orderBy',
        ]))
            ->whereStatus('A')
            ->leftJoin('countries', 'countries.id', 'tax_groups.country_id')
            ->leftJoin('states', 'states.id', 'tax_groups.state_id')
            ->select('tax_groups.id', 'tax_groups.name', 'tax_groups.description', 'tax_groups.created_at', 'tax_groups.country_id', 'tax_groups.state_id', 'countries.name as country_name', 'states.name as state_name')
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'tax_groups' => $tax_groups,
            'taxGroupTotalCount' => TaxGroups::whereStatus('A')->count(),
        ], "message" => "Tax group Index"]];
        // LogsDev::finishLog($log, $res, $time, 'D', "Tax group Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'tax_groups' => $tax_groups,
            'taxGroupTotalCount' => TaxGroups::whereStatus('A')->count(),
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
    public function store(TaxGroupRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxGroupController", "store");
        ////////////////

        $tax_groups = TaxGroups::createTaxGroup($request);

        // Guardar grupo de impuestos
        if (isset($request['taxes'])) {
            foreach ($request['taxes'] as $tax) {
                if ($tax['id'] != null) {
                    $TaxGroup = new TaxGroup();
                    $TaxGroup->taxes_id = $tax['id'];
                    $TaxGroup->tax_groups_id = $tax_groups['id'];
                    $TaxGroup->save();
                }
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'tax_groups' => $tax_groups,
        ], "message" => "Tax group store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Tax group store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'tax_groups' => $tax_groups
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxGroupController", "show");
        ////////////////

        $tax_groups = TaxGroups::where('tax_groups.id', $id)
            ->leftJoin('countries', 'countries.id', 'tax_groups.country_id')
            ->leftJoin('states', 'states.id', 'tax_groups.state_id')
            ->select('tax_groups.*', 'countries.id as country_id', 'countries.name as country_name', 'states.id as state_id', 'states.name as state_name')
            ->first();

        if ($tax_groups['country_id'] != null) {
            $tax_country = Country::where('id', $tax_groups['country_id'])->first();
            $tax_groups['countries'] = $tax_country;
        }

        if ($tax_groups['state_id'] != null) {
            $tax_state = State::where('id', $tax_groups['state_id'])->first();
            $tax_groups['states'] = $tax_state;
        }

        switch ($tax_groups['status']) {
            case 'A':
                $tax_groups['status'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $tax_groups['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            default:
                break;
        }

        $taxes = TaxGroups::showTaxGroup($id);

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'tax_groups' => $tax_groups,
            'taxes' => $taxes,
        ], "message" => "Tax group show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Tax group show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'tax_groups' => $tax_groups,
            'taxes' => $taxes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaxGroupRequest $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxGroupController", "update");
        ////////////////

        $tax_groups = TaxGroups::find($id);

        $data = $request->validated();
        $tax_groups->update($data);
        $tax_groups->city = $request->input('city');
        $tax_groups->county = $request->input('county');
        $tax_groups->save();

        // Eliminar grupo de impuestos
        $TaxGroups = TaxGroup::where('tax_groups_id', $tax_groups['id'])->get();
        foreach ($TaxGroups as $TaxGroup) {
            $TaxGroup->deleted_at = Carbon::now();
            $TaxGroup->save();
        }

        // Guardar grupo de impuestos
        if (isset($request->taxGroupLeftt)) {

            foreach ($request->taxGroupLeft as $tax) {
                if ($tax['id'] != null) {
                    $TaxGroup = new TaxGroup();
                    $TaxGroup->taxes_id = $tax['id'];
                    $TaxGroup->tax_groups_id = $tax_groups['id'];
                    $TaxGroup->save();
                }
            }
        }

        $update_taxes = TaxGroups::updateTaxGroups(collect($request->taxes), $tax_groups->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'tax_groups' => $tax_groups,
        ], "message" => "Tax group update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Tax group update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'tax_groups' => $tax_groups,
            'update_taxes' => $update_taxes,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteTaxGroupRequest $request)
    {
        //Validate PbxPackages
        $isActiveInPackage = DB::table('package_tax_groups')->where('tax_group_id', $request->ids[0])->first();

        // Delete TaxGroups
        $tax_groups = TaxGroups::find($request->ids[0]);
        if (! $isActiveInPackage) {
            if ($tax_groups != null) {
                \DB::table('tax_groups')->delete($request->ids[0]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Tax group has active packages"
            ]);
        }
        //

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxGroupController", "destroy");
        //////////////////

        //TaxGroups::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Tax group deleted successfully',
        ], "message" => "Tax group deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Tax group deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Tax group deleted successfully',
        ]);
    }

    public function taxes()
    {
        return TaxType::all();
    }
}
