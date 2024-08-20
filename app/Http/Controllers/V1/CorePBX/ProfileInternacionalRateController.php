<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteInternacionalRateRequest;
use Crater\Http\Requests\PbxInternacionalRateRequest;
use Crater\Imports\InternacionalRateImport;
use Crater\Models\CustomRateGroupItems;
use Crater\Models\InternationalRate;
use Crater\Models\LogsDev;
use Crater\Models\ProfileInternacionalRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class ProfileInternacionalRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileInternacionalRateController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $groupids = ProfileInternacionalRate::all()->pluck('id')->toarray();

        $int_rate_ids_no_group = [];
        $int_rate_ids = [];

        if(! is_null($request['prefixrate_groups_id'])) {
            if (in_array("0", $request['prefixrate_groups_id'])) {
                $pluck_ids_international = DB::table('international_rate_prefixrate_group')->where('deleted_at', null)
                ->pluck('int_rate_id');

                $int_rate_ids_no_group = ProfileInternacionalRate::whereNotIn('id', $pluck_ids_international)
                ->where('deleted_at', null)
                ->pluck('id')->toArray();
            }

            if($request['prefixrate_groups_id']) {
                $int_rate_ids = DB::table('international_rate_prefixrate_group')
                                            ->whereIn('prefixrate_id', $request['prefixrate_groups_id'])
                                            ->where('deleted_at', null)
                                            ->pluck('int_rate_id')->toArray();
            }

            $groupids = array_merge($int_rate_ids_no_group, $int_rate_ids);
        }

        $internacionals = ProfileInternacionalRate::with('country')
        ->applyFilters($request->only([
            'country_id',
            'prefix_id',
            'country_name',
            'prefix',
            'from',
            'to',
            'prefix_type',
            'name',
            'orderByField',
            'orderBy',
        ]))
        ->select('international_rate.*')
        ->whereIn('id', $groupids)
        ->latest()
        ->paginateData($limit);

        $count = ProfileInternacionalRate::count();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'internacionals' => $internacionals,
            'internacionalTotalCount' => $count
        ], "message" => "ProfileInternacionalRateController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileInternacionalRateController index");
        /////////////////////////////////////////

        return response()->json([
            'internacionals' => $internacionals,
            'internacionalTotalCount' => $count
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
    public function store(PbxInternacionalRateRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileInternacionalRateController", "store");
        ////////////////

        $internacional = ProfileInternacionalRate::createRate($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'departament' => $internacional,
        ], "message" => "Internacional Rate store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Internacional Rate store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'internacional' => $internacional,
            'success' => 'Custom destination save successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ProfileInternacionalRate  $profileInternacionalRate
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileInternacionalRate", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resProfileDID = ProfileDIDRequest::find($id);
        $internacional = ProfileInternacionalRate::find($id);
        $internacional->load('ratePrefixGroups');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'internacional' => $internacional,
        ], "message" => "ProfileInternacionalRate get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileInternacionalRate show");
        /////////////////////////////////////////

        return response()->json([
            'internacional' => $internacional,
            'success' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\ProfileInternacionalRate  $profileInternacionalRate
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileInternacionalRate $profileInternacionalRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\ProfileInternacionalRate  $profileInternacionalRate
     * @return \Illuminate\Http\Response
     */
    public function update(PbxInternacionalRateRequest $request, $id)
    {
        \Log::debug($request->all());
        $data["prefix"] = $request->prefijo;
        $data['name'] = $request->name;
        /*  $data['prefixrate_groups_id']=$request->prefixrate_groups_id; */
        $data["country_id"] = $request->country_id;
        $data["status"] = $request->status;
        $data['category'] = $request->category;
        $data["rate_per_minute"] = $request->rate_per_minutes;
        $data["typecustom"] = $request->typecustom;
        $data["from"] = $request->from;
        $data["to"] = $request->to;


        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxInternacionalRateRequest", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo


        /* $data = $request->validated(); */
        $internacional = ProfileInternacionalRate::find($id);
        $internacional->update($data);

        // Eliminar los grupos asociados
        // ProfileInternacionalRate::deletePrefixGroups($internacional);

        if ($request->has('prefixrate_groups_id')) {
            // Asociar nuevos grupos

            ProfileInternacionalRate::createPrefixGroups($internacional, $request);
        }



        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'internacional' => $internacional,
        ], "message" => "PbxInternacionalRateRequest put"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxInternacionalRateRequest update");

        /////////////////////////////////////////
        return response()->json([
            'internacional' => $internacional,
            'message' => 'Custom destination succesfully',
            'success' => true
        ]);
    }

    public function updatePrefixInternational(Request $request)
    {
        $internationalRate = InternationalRate::find($request->id);

        if($internationalRate) {
            $internationalRate->country_id = $request->country_id;
            $internationalRate->status = $request->status;
            $internationalRate->rate_per_minute = $request->multiple[0]['rate_per_minutes'];
            $internationalRate->name = $request->multiple[0]['name'];
            $internationalRate->category = $request->category;

            if($request->multiple[0]['typecustom'] == 'P') {
                $internationalRate->typecustom = $request->multiple[0]['typecustom'];
                $internationalRate->prefix = $request->multiple[0]['prefijo'];
                $internationalRate->from = null;
                $internationalRate->to = null;
            }
            if($request->multiple[0]['typecustom'] == 'FT') {
                $internationalRate->typecustom = $request->multiple[0]['typecustom'];
                $internationalRate->from = $request->multiple[0]['from'];
                $internationalRate->to = $request->multiple[0]['to'];
                $internationalRate->prefix = null;
            }
            $internationalRate->save();
        }

        return response()->json([
            'message' => 'Prefix updated successfully',
            'success' => true
        ]);

    }

    public function deletePrefixInternational(Request $request)
    {

        $customRateGroupItems = CustomRateGroupItems::where('prefixrate_id', $request->id_prefix_group)
                                                    ->where('int_rate_id', $request->id_prefix)
                                                    ->first();

        //$internationalRate = InternationalRate::where('id', $request->id_prefix)->delete();

        if($customRateGroupItems) {
            $customRateGroupItems->deleted_at = Carbon::now();
            $customRateGroupItems->save();
        }

        return response()->json([
            'message' => 'Prefix deleted successfully',
            'success' => true
        ]);

    }

    public function modifySelected(Request $request)
    {
        $country_id = $request->data['country_id'] != null ? $request->data['country_id'] : null;
        $category = $request->data['category'] != null ? $request->data['category'] : null;
        $rate_per_minute = $request->data['rate_per_minutes'] != null ? $request->data['rate_per_minutes'] : null;
        foreach ($request->internationalsSelectedIds as $id) {
            $internationalRate = InternationalRate::find($id);

            if($internationalRate) {
                if($country_id != null) {
                    $internationalRate->country_id = $country_id;
                }
                if($category != null) {
                    $internationalRate->category = $category;
                }
                if($rate_per_minute != null) {
                    $internationalRate->rate_per_minute = $rate_per_minute;
                }
                $internationalRate->save();

                if($request->data['IsOrder']) {
                    $update_order = DB::table('international_rate_prefixrate_group')
                                                            ->where('int_rate_id', $internationalRate->id)
                                                            ->where('prefixrate_id', $request->data['current_group_id'])
                                                            ->where('deleted_at', null)
                                                            ->update(['order' => $request->data['order']]);
                }
            }

        }

        // Add or Delete
        if(! is_null($request->data['prefixrate_groups']) && ! is_null($request->data['status'])) {
            foreach ($request->data['prefixrate_groups'] as $group) {
                foreach ($request->internationalsSelectedIds as $international_rate_id) {
                    $isExist = DB::table('international_rate_prefixrate_group')
                                        ->where('prefixrate_id', $group["id"])
                                        ->where('int_rate_id', $international_rate_id)
                                        ->where('deleted_at', null)
                                        ->get();

                    $internationalRate = InternationalRate::find($international_rate_id);

                    if(count($isExist) > 0) {
                        if($request->data['status'] == "D") {
                            DB::table('international_rate_prefixrate_group')
                                ->where('int_rate_id', $internationalRate->id)
                                ->where('prefixrate_id', $group["id"])
                                ->delete();
                        }
                    } else {
                        if($request->data['status'] == "A") {
                            DB::table('international_rate_prefixrate_group')->insert([
                                'order' => $request->data['order'],
                                'int_rate_id' => $internationalRate->id,
                                'prefixrate_id' => $group["id"],
                                'company_id' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'deleted_at' => null,
                            ]);
                        }
                    }
                }

            }
        }
        //

        return response()->json([
            'message' => 'Prefixes modified successfully',
            'success' => true
        ]);

    }

    public function modifyAll(Request $request)
    {
        $country_id = $request->data['country_id'] != null ? $request->data['country_id'] : null;
        $category = $request->data['category'] != null ? $request->data['category'] : null;
        $rate_per_minute = $request->data['rate_per_minutes'] != null ? $request->data['rate_per_minutes'] : null;

        foreach ($request->internationals as $international) {
            $internationalRate = InternationalRate::find($international['id']);
            if ($internationalRate) {
                if($country_id != null) {
                    $internationalRate->country_id = $country_id;
                }
                if($category != null) {
                    $internationalRate->category = $category;
                }
                if($rate_per_minute != null) {
                    $internationalRate->rate_per_minute = $rate_per_minute;
                }
                $internationalRate->save();

                if($request->data['IsOrder']) {
                    $update_order = DB::table('international_rate_prefixrate_group')
                                                            ->where('int_rate_id', $internationalRate->id)
                                                            ->where('prefixrate_id', $request->data['current_group_id'])
                                                            ->where('deleted_at', null)
                                                            ->update(['order' => $request->data['order']]);
                }
            }
        }

        // Add or Delete
        if(! is_null($request->data['prefixrate_groups']) && ! is_null($request->data['status'])) {
            foreach ($request->data['prefixrate_groups'] as $group) {
                foreach ($request->internationals as $international_rate) {
                    $isExist = DB::table('international_rate_prefixrate_group')
                                        ->where('prefixrate_id', $group["id"])
                                        ->where('int_rate_id', $international_rate["id"])
                                        ->where('deleted_at', null)
                                        ->get();

                    $internationalRate = InternationalRate::find($international_rate["id"]);

                    if(count($isExist) > 0) {
                        if($request->data['status'] == "D") {
                            DB::table('international_rate_prefixrate_group')
                                ->where('int_rate_id', $internationalRate->id)
                                ->where('prefixrate_id', $group["id"])
                                ->delete();
                        }
                    } else {
                        if($request->data['status'] == "A") {
                            DB::table('international_rate_prefixrate_group')->insert([
                                'order' => $request->data['order'],
                                'int_rate_id' => $internationalRate->id,
                                'prefixrate_id' => $group["id"],
                                'company_id' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                                'deleted_at' => null,
                            ]);
                        }
                    }
                }

            }
        }
        //

        return response()->json([
            'message' => 'Prefixes modified successfully',
            'success' => true
        ]);

    }

    public function delete(DeleteInternacionalRateRequest $request)
    {
        // DeleteInternacionalRateRequest
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxInternacionalRateRequest", "destroy");
        //////////////////
        $internacional = ProfileInternacionalRate::find($request->id);
        // Eliminar los grupos asociados
        ProfileInternacionalRate::deletePrefixGroups($internacional);

        ProfileInternacionalRate::destroy($request->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Internacional Rate deleted successfully',
        ], "message" => "Internacional Rate deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Internacional Rate deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Internacional Rate deleted successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\ProfileInternacionalRate  $profileInternacionalRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileInternacionalRate $profileInternacionalRate)
    {
        //
    }

    public function loadPrefixRate(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "loadPrefixRate", "loadPrefixRate");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resProfileDID = ProfileDIDRequest::find($id);
        $internacional = DB::table('prefixrate_groups')->where('status', '=', 'A')->whereNULL('deleted_at')->get();
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'internacional' => $internacional,
        ], "message" => "loadPrefixRate get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileInternacionalRate loadPrefixRate");
        /////////////////////////////////////////

        return response()->json([
            'internacional' => $internacional,
            'success' => true
        ]);
    }

    public function importCsv(Request $request)
    {
        /* dd($request->file('file')); */
        $result = Excel::toArray(new InternacionalRateImport(), $request->file('file'));

        /* Excel::import(new InternacionalRateImport, $request->file('file')); */
        /* return $array; */
        return response()->json([
            'result' => $result,
            'success' => true
        ]);
    }
}
