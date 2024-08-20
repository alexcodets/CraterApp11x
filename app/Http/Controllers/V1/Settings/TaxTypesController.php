<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TaxTypeRequest;
use Crater\Models\CustomerPackageTax;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PackageTaxTypes;
use Crater\Models\PbxPackageTaxTypes;
use Crater\Models\PbxPackageTaxTypesCdr;
use Crater\Models\PbxServicesTaxTypes;
use Crater\Models\PbxServicesTaxTypesCdr;
use Crater\Models\Tax;
use Crater\Models\TaxGroupType;
use Crater\Models\TaxType;
use Illuminate\Http\Request;

class TaxTypesController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 5;

        $taxTypes = TaxType::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'tax_type_id',
                'search',
                'orderByField',
                'orderBy',
                'for_cdr',
            ]))
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxTypes' => $taxTypes,
        ], "message" => "TaxTypesController"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxTypesController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Types", "List", "admin/settings/tax-types", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'taxTypes' => $taxTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxTypeRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();

        $data['company_id'] = $request->header('company');
        $data['state_id'] = $request->input('state_id');
        $data['country_id'] = $request->input('country_id');
        $data['city'] = $request->input('city');
        $data['county'] = $request->input('county');
        $data['for_cdr'] = $request->for_cdr;

        // return ["data" => $data, "req" => $request];

        $taxType = TaxType::create($data);

        if (count($request->tax_groups) > 0) {
            foreach ($request->tax_groups as $tax) {
                $TaxGroupType = new TaxGroupType();
                $TaxGroupType->tax_groups_id = $tax['id'];
                $TaxGroupType->tax_types_id = $taxType['id'];
                $TaxGroupType->save();
            }
        }

        // Logs por modulo
        LogsModule::createLog("Tax Types", "Create", "admin/settings/tax-types", $taxType->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxType->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxType' => $taxType,
        ], "message" => "GetUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "GetUserSettingsController");
        /////////////////////////////////////////

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxType $taxType, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxType' => $taxType]);
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $taxType['tax_groups'] = TaxGroupType::leftJoin('tax_groups', 'tax_groups.id', 'tax_group_types.tax_groups_id')
            ->select('tax_group_types.id as tax_group_types_id', 'tax_groups.id', 'tax_groups.name')
            ->where('tax_group_types.deleted_at', '=', null)
            ->where('tax_group_types.tax_types_id', $taxType['id'])->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxType' => $taxType,
        ], "message" => "TaxTypesController show"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxTypesController show");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Types", "View", "admin/settings/tax-types", $taxType->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxType->name);

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function update(TaxTypeRequest $request, TaxType $taxType)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxType' => $taxType]);
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $taxType->update($request->validated());
        $taxType->city = $request->input('city');
        $taxType->county = $request->input('county');
        $taxType->for_cdr = $request->input('for_cdr');
        $taxType->save();

        //Log::debug('---- tax typr up ----');
        //Log::debug($request->validated());

        // Eliminar grupo de impuestos asociados a un tipo de impuesto
        $TaxGroupTypes = TaxGroupType::where('tax_types_id', $taxType['id'])->get();
        foreach ($TaxGroupTypes as $TaxGroupType) {
            $TaxGroupType->deleted_at = Carbon::now();
            $TaxGroupType->save();
        }

        // Guardar grupo de impuestos asociados a un tipo de impuesto
        if (count($request->tax_groups) > 0) {
            foreach ($request->tax_groups as $tax) {
                $TaxGroupType = new TaxGroupType();
                $TaxGroupType->tax_groups_id = $tax['id'];
                $TaxGroupType->tax_types_id = $taxType['id'];
                $TaxGroupType->save();
            }
        }

        // Actualizar todos los impuestos asociados con items

        $taxesUpdate = Tax::where('tax_type_id', $taxType->id)
            ->whereNotNull('item_id')
            ->whereNull('invoice_item_id')
            ->whereNull('estimate_item_id')
            ->whereNull('package_id')
            ->whereNull('package_item_id')
            ->whereNull('pbx_package_id')
            ->whereNull('pbx_package_item_id')
            ->whereNull('pbx_service_item_id')->get();

        foreach ($taxesUpdate as $taxeUpdate) {
            $itemPrice = Item::where('id', $taxeUpdate->item_id)->pluck('price')->first() / 100;

            $taxeUpdate->amount = ($itemPrice * $taxType->percent / 100) * 100;
            $taxeUpdate->percent = $taxType->percent;
            $taxeUpdate->save();

        }

        // ->update([
        //     'amount' => $taxType->amount,
        //     'percent' => $taxType->percent,
        // ]);

        // Logs por modulo
        LogsModule::createLog("Tax Types", "Update", "admin/settings/tax-types", $taxType->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxType->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxType' => $taxType,
        ], "message" => "TaxTypesController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxTypesController update");
        /////////////////////////////////////////

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxType $taxType, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxType' => $taxType]);
        $log = LogsDev::initLog($request, "", "D", "TaxTypesController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($taxType->taxes() && $taxType->taxes()->count() > 0) {
            // Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => ['success' => false], "message" => "destroy failed"];
            LogsDev::finishLog($log, $res, $time, 'D', "destroy failed");

            /////////////////////////////////////////
            return response()->json(['success' => false]);
        } elseif (
            CustomerPackageTax::where('tax_type_id', $taxType->id)->exists() ||
            PbxServicesTaxTypes::where('tax_types_id', $taxType->id)->exists() ||
            PbxServicesTaxTypesCdr::where('tax_types_id', $taxType->id)->exists() ||
            PbxPackageTaxTypes::where('tax_types_id', $taxType->id)->exists() ||
            PbxPackageTaxTypesCdr::where('tax_types_id', $taxType->id)->exists() ||
            PackageTaxTypes::where('tax_types_id', $taxType->id)->exists()
        ) {
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [
                'success' => false,
            ], "message" => "destroy failed"];
            LogsDev::finishLog($log, $res, $time, 'D', "destroy failed");
            /////////////////////////////////////////

            return response()->json([
                'success' => false,
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("Tax Types", "delete", "admin/settings/tax-types", $taxType->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxType->name);

        $taxType->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "destroy succes"];
        LogsDev::finishLog($log, $res, $time, 'D', "destroy success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }
}
