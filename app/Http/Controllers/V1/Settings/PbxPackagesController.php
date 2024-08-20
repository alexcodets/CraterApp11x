<?php

namespace Crater\Http\Controllers\V1\Settings;

//
use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxPackagesRequest;
use Crater\Models\AditionalCharges;
// models
use Crater\Models\CompanySetting;
// models
use Crater\Models\CustomDidGroup;
use Crater\Models\LogsDev;
use Crater\Models\PbxPackageItem;
use Crater\Models\PbxPackageItemGroup;
use Crater\Models\PbxPackages;
use Crater\Models\PbxPackageTaxTypes;
use Crater\Models\PbxPackageTaxTypesCdr;
use Crater\Models\PbxServices;
use Crater\Models\ProfileDID;
use Crater\Models\ProfileDidCustomDidGroups;
use Crater\Models\ProfileDidTollFree;
use Crater\Models\Tax;
use Crater\Models\TaxType;
use Crater\Models\TollFreeCustomDidGroup;
use Crater\Models\User;
use DB;
use Illuminate\Http\Request;
use Log;

class PbxPackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idCustomer)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // user status payment
        $status_payment = 'prepaid';
        $customer = User::select('status_payment')->where('id', '=', $idCustomer)->get();

        if ($customer != null && $customer->count() > 0) {
            if ($customer[0]->status_payment || strlen($customer[0]->status_payment) > 0) {
                $status_payment = $customer[0]->status_payment;
            }
        }

        $resPbxPackages = PbxPackages::with(
            'server',
            'profileDid2',
            'profileExtensions',
            'prefixrateGroup',
            'prefixrateGroupOutbound',
            'items',
            'taxTypes'
        )
            ->applyFilters($request->only([
                'search',
                'pbx_package_name',
                'packages_number',
                'status_payment',
                'orderByField',
                'orderBy',
            ]))
            ->select('pbx_packages.*')
            ->paginate(10);

        // $resPbxPackages =  $resPbxPackages->unique();

        if ($customer != null && $customer->count() > 0) {
            $resPbxPackages = PbxPackages::applyFilters($request->only([
                'search',
                'pbx_package_name',
                'packages_number',
                'orderByField',
                'orderBy',
            ]))
                ->with(
                    'server',
                    'profileDid2',
                    'profileExtensions',
                    'prefixrateGroup',
                    'prefixrateGroupOutbound',
                    'items',
                    'taxTypes'
                )
                ->select('pbx_packages.*')
                ->where('pbx_packages.status_payment', '=', $status_payment)
                ->get();
            $resPbxPackages = $resPbxPackages->unique();
        }

        foreach ($resPbxPackages as $package) {
            //    //Log::debug($package);
            if ($package->template_did_id) {
                $aditional_charges = AditionalCharges::where('aditional_charges.profile_did_id', '=', $package->template_did_id)->get();
                $package->profile_did_aditional_charges = $aditional_charges;

                ///// cambios

                $profile = ProfileDID::find($package->template_did_id);

                if (isset($profile)) {
                    $vec1 = ProfileDidCustomDidGroups::where("profile_did_id", $profile->id)->whereNULL("deleted_at")->pluck('custom_did_group_id');
                    if (isset($vec1)) {
                        $custom_dids = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->whereNULL("deleted_at")->pluck('toll_free_did_id');

                        $package->custom_did_groups = ProfileDidTollFree::whereIN('id', $custom_dids)->orderBy('prefijo', 'desc')->get();
                        foreach ($package->custom_did_groups as $prefix) {

                            $prefix["name_prefix"] = $profile->name;

                            $first = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->where("toll_free_did_id", $prefix->id)->whereNULL("deleted_at")->first();

                            if ($first != null) {
                                $second = CustomDidGroup::where("id", $first->custom_did_group_id)->first();
                                if ($second != null) {
                                    $prefix["name_prefix"] = $second->name;
                                }
                            }
                        }
                    }
                }

            }

            if ($package->template_extension_id) {
                $aditional_charges = AditionalCharges::where('aditional_charges.profile_extension_id', '=', $package->template_extension_id)->get();
                $package->profile_extensions_aditional_charges = $aditional_charges;
            }

            //Log::debug($package);
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxPackages' => $resPbxPackages,
        ], "message" => "PbxPackagesController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxPackagesController index");
        ////////////////////////////////////////////////////////////////////////////

        return response()->json([
            'pbxPackages' => $resPbxPackages,
        ]);
    }

    public function indexPBX(Request $request, $idCustomer)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        // user status payment
        $status_payment = 'prepaid';
        $customer = User::select('status_payment')->where('id', '=', $idCustomer)->get();
        if ($customer != null && $customer->count() > 0) {
            if ($customer[0]->status_payment || strlen($customer[0]->status_payment) > 0) {
                $status_payment = $customer[0]->status_payment;
            }
        }
        $resPbxPackages = PbxPackages::with(
            'server',
            'profileDid2',
            'profileExtensions',
            'prefixrateGroup',
            'prefixrateGroupOutbound',
            'items.taxes',
            'taxTypesCdr',
            'taxTypes'
        )
            ->applyFilters($request->only([
                'search',
                'pbx_package_name',
                'packages_number',
                'status_payment',
                'orderByField',
                'orderBy']))
            ->select('pbx_packages.*')
            ->whereHas('server', function ($query) {
                $query->where('status', '!=', 'A');
            })
            ->get();
        $resPbxPackages = $resPbxPackages->unique();
        if ($customer != null && $customer->count() > 0) {
            $resPbxPackages = PbxPackages::applyFilters($request->only([
                'search',
                'pbx_package_name',
                'packages_number',
                'orderByField',
                'orderBy',
            ]))
                ->with(
                    'server',
                    'profileDid2',
                    'profileExtensions',
                    'prefixrateGroup',
                    'prefixrateGroupOutbound',
                    'items.taxes',
                    'taxTypesCdr',
                    'taxTypes'
                )->select('pbx_packages.*')->where('pbx_packages.status_payment', '=', $status_payment)
            // filtrar por server status = A
                ->whereHas('server', function ($query) {
                    $query->where('status', '=', 'A');
                })
                ->get();
            $resPbxPackages = $resPbxPackages->unique();
        }
        foreach ($resPbxPackages as $package) {
            //Log::debug($package);
            if ($package->template_did_id) {
                $aditional_charges = AditionalCharges::where('aditional_charges.profile_did_id', '=', $package->template_did_id)->get();
                $package->profile_did_aditional_charges = $aditional_charges;
                ///// cambios
                $profile = ProfileDID::find($package->template_did_id);
                $idProfile = $profile->id;
                $vec1 = ProfileDidCustomDidGroups::where("profile_did_id", $idProfile)->whereNULL("deleted_at")->pluck('custom_did_group_id');
                $custom_dids = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->whereNULL("deleted_at")->pluck('toll_free_did_id');
                $package->custom_did_groups = ProfileDidTollFree::whereIN('id', $custom_dids)->orderBy('prefijo', 'desc')->get();
                foreach ($package->custom_did_groups as $prefix) {
                    $prefix["name_prefix"] = $profile->name;

                    $first = TollFreeCustomDidGroup::whereIN("custom_did_group_id", $vec1)->where("toll_free_did_id", $prefix->id)->whereNULL("deleted_at")->first();

                    if ($first != null) {
                        $second = CustomDidGroup::where("id", $first->custom_did_group_id)->first();
                        if ($second != null) {
                            $prefix["name_prefix"] = $second->name;
                        }
                    }
                }
            }
            if ($package->template_extension_id) {
                $aditional_charges = AditionalCharges::where('aditional_charges.profile_extension_id', '=', $package->template_extension_id)->get();
                $package->profile_extensions_aditional_charges = $aditional_charges;
            }
            //Log::debug($package);
        }

        // Custom Destination Groups By PbxPackages
        foreach ($resPbxPackages as $key => $package) {
            $prefixrate_groups_ids = DB::table('pbx_packages_prefixrate_groups')->where("pbx_package_id", $package->id)
                                                 ->where('deleted_at', null)
                                                 ->pluck('prefixrate_group_id');

            $custom_destination_groups = DB::table('prefixrate_groups')
                                        ->whereIn("id", $prefixrate_groups_ids)
                                        ->where('deleted_at', null)
                                        ->get();
            $package["custom_destination_groups"] = $custom_destination_groups;
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true,
            "response" => ['pbxPackages' => $resPbxPackages],
            "message" => "PbxPackagesController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxPackagesController index");

        ////////////////////////////////////////////////////////////////////////////
        return response()->json([
            'pbxPackages' => $resPbxPackages,
        ]);
    }

    /**
     * add pbx packages to taxes types
     */
    public function addPbxPackageTaxTypes($tax, $pbx_package_id, $company_id)
    {
        $res = [];
        $newPackageTaxTypes = new PbxPackageTaxTypes();
        $newPackageTaxTypes->pbx_package_id = $pbx_package_id;
        $newPackageTaxTypes->company_id = $company_id;
        $newPackageTaxTypes->tax_types_id = $tax['id'];
        $newPackageTaxTypes->name = $tax['name'];
        $newPackageTaxTypes->percent = $tax['percent'];
        $newPackageTaxTypes->compound_tax = $tax['compound_tax'];
        $res[] = $newPackageTaxTypes->save();

        return $res;
    }

    /**
     * add pbx packages to taxes types
     */
    public function addPbxPackageTaxTypesCdrs($tax, $pbx_package_id, $company_id)
    {
        $res = [];
        $newPackageTaxTypes = new PbxPackageTaxTypesCdr();
        $newPackageTaxTypes->pbx_package_id = $pbx_package_id;
        $newPackageTaxTypes->company_id = $company_id;
        $newPackageTaxTypes->tax_types_id = $tax['id'];
        $newPackageTaxTypes->name = $tax['name'];
        $newPackageTaxTypes->percent = $tax['percent'];
        $newPackageTaxTypes->compound_tax = $tax['compound_tax'];
        $res[] = $newPackageTaxTypes->save();

        return $res;
    }

    /**
     * add pbx packages to taxes
     */
    public function addPbxPackageTaxes($tax, $package_id, $item_id, $package_item_id, $company_id, $pbx_package_id)
    {
        $res = [];

        // $taxes->each(function ($tax) use ($package_id, $res, $item_id, $package_item_id, $company_id, $pbx_package_id) {
        if (! empty($tax['name'])) {
            $newTax = new Tax();
            $newTax->package_item_id = $package_item_id;
            $newTax->item_id = $item_id;
            $newTax->company_id = $company_id;
            $newTax->amount = $tax['amount'];
            $newTax->name = $tax['name'];
            $newTax->percent = $tax['percent'];
            $newTax->compound_tax = $tax['compound_tax'];
            $newTax->tax_type_id = $tax['tax_type_id'];
            $newTax->package_id = $package_id;
            $newTax->pbx_package_id = $pbx_package_id;
            $res[] = $newTax->save();
        }
        // });

        return $res;
    }

    public function addPackageItems($items, $pbx_package_id, $company_id)
    {
        $res = [];

        $items->each(function ($item) use ($pbx_package_id, $res, $company_id) {

            // if ($item['item_id'] != null) {
            $newPackageItems = new PbxPackageItem();
            $newPackageItems->pbx_package_id = $pbx_package_id;
            $newPackageItems->item_group_id = $item['item_group_id'];
            $newPackageItems->items_id = $item['item_id'];
            $newPackageItems->discount_type = $item['discount_type'];
            $newPackageItems->quantity = $item['quantity'];
            $newPackageItems->discount = $item['discount'];
            $newPackageItems->discount_val = $item['discount_val'];
            $newPackageItems->price = $item['price'];
            $newPackageItems->tax = $item['tax'];
            $newPackageItems->total = $item['total'];
            $newPackageItems->description = $item['description'];
            $newPackageItems->name = $item['name'];
            $newPackageItems->company_id = $company_id;

            $newPackageItems->end_period_number = $item['end_period_number'];
            $newPackageItems->end_period_act = $item['end_period_act'];

            // Verificar si 'end_period_number' está en null, vacío o no es un número entero
            if (empty($newPackageItems->end_period_number) || ! is_numeric($newPackageItems->end_period_number) || intval($newPackageItems->end_period_number) != $newPackageItems->end_period_number) {
                $newPackageItems->end_period_number = 1;
            }
            $res[] = $newPackageItems->save();
            if (count($item['taxes']) > 0) {
                $this->addPbxPackageItemTaxes(collect($item['taxes']), $newPackageItems->id, $company_id);
            }
            // }
        });


        return $res;
    }

    public function addPbxPackageItemTaxes($taxes, $pbx_package_item_id, $company_id)
    {
        // validar si el item tiene tax
        $taxes->each(function ($tax) use ($pbx_package_item_id, $company_id) {

            if ($tax['amount'] != null) {
                $newTax = new Tax();
                $newTax->company_id = $company_id;
                $newTax->amount = $tax['amount'];
                $newTax->name = $tax['name'];
                $newTax->percent = $tax['percent'];
                $newTax->tax_type_id = $tax['tax_type_id'];
                $newTax->pbx_package_item_id = $pbx_package_item_id;
                $newTax->save();
            }
        });
    }

    public function addPackageTaxes($taxes, $pbx_package_id, $item_id, $package_item_id, $company_id)
    {
        $res = [];
        $taxes->each(function ($tax) use ($pbx_package_id, $res, $item_id, $package_item_id, $company_id) {
            if (! empty($tax['name'])) {
                $newTax = new Tax();
                $newTax->$pbx_package_id = $pbx_package_id;
                $newTax->amount = $tax['amount'];
                $newTax->name = $tax['name'];
                $newTax->percent = $tax['percent'];
                $newTax->compound_tax = $tax['compound_tax'];
                $newTax->tax_type_id = $tax['tax_type_id'];
                $newTax->package_item_id = $package_item_id;
                $newTax->item_id = $item_id;
                $newTax->company_id = $company_id;
                $res[] = $newTax->save();
            }
        });

        return $res;
    }

    public function addPackageGroupItems($items, $pbx_package_id, $company_id)
    {
        $res = [];
        $items->each(function ($item) use ($pbx_package_id, $res, $company_id) {
            if ($item['id'] != null) {
                $newPackageItemGroup = new PbxPackageItemGroup();
                $newPackageItemGroup->pbx_package_id = $pbx_package_id;
                $newPackageItemGroup->company_id = $company_id;
                $newPackageItemGroup->item_group_id = $item['id'];
                $res[] = $newPackageItemGroup->save();
            }
        });

        return $res;
    }

    public function updatePackageItems($items, $pbx_package_id, $company_id)
    {
        PbxPackageItem::where('pbx_package_id', $pbx_package_id)->delete();
        $items->each(function ($item) use ($pbx_package_id, $company_id) {
            if ($item['item_id'] != null) {
                $deleteItem = Tax::find($item['id']);
                if ($deleteItem != null) {
                    $deleteItem->delete();
                }
            }
        });
        /*  Tax::where('pbx_package_id', $pbx_package_id)->delete(); */
        if (count($items) > 0) {
            $this->addPackageItems(collect($items), $pbx_package_id, $company_id);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Crater\Http\Requests\PbxPackagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PbxPackagesRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $data['taxesCdr'] = $request->get("taxesCdr");
        $prefix = CompanySetting::where("option", "=", 'packages_pbx_prefix')->where('company_id', "=", Auth::user()->company_id)->first();

        $company_id = 1;
        $user_id = 1;
        if ($data['discount_start_date'] && $data['discount_start_date']) {
            $data['discount_start_date'] = date('Y-m-d', strtotime($data['discount_start_date']));
            $data['discount_end_date'] = date('Y-m-d', strtotime($data['discount_end_date']));
        }
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;
        ini_set('memory_limit', '-1');
        // print_r($request);
        $data['pbx_server_id'] = $request->input('dropdown_server');

        if ($data['type_time_increment'] == null) {
            $data['type_time_increment'] = "sec";
        }
        if ($data['rate'] == null) {
            $data['rate'] = 0;
        }

        $resPbxPackages = PbxPackages::create($data);
        $pbx_package_id = $resPbxPackages->id;

        // Insert into package_tax_groups (Grupo de taxes pertenecientes a un package)
        if(array_key_exists("tax_groups", $data)) {
            if(! is_null($request['tax_groups'])) {
                if(count($request['tax_groups']) > 0) {
                    foreach ($request['tax_groups'] as $tax_gp) {
                        DB::table('package_tax_groups')->insert([
                            'package_id' => null,
                            'pbx_package_id' => $pbx_package_id,
                            'tax_group_id' => $tax_gp['id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }
            }
        }
        //

        // Insert into pbx_packages_prefixrate_groups (Grupos de Custom Destination "inbound-Outbound)
        if(array_key_exists("custom_destination_groups", $data)) {
            if(count($request['custom_destination_groups']) > 0) {
                foreach ($request['custom_destination_groups'] as $group) {
                    DB::table('pbx_packages_prefixrate_groups')->insert([
                        'pbx_package_id' => $pbx_package_id,
                        'prefixrate_group_id' => $group['id'],
                        'type' => $group['type'],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
                }
            }

        }
        //


        foreach ($request->cdrStatus as $key => $value) {
            $resPbxPackages->cdrStatus()->create([
                'pbx_packages_id' => $resPbxPackages->id,
                'status' => $value,
            ]);
        }

        $packages_number = '';
        if ($resPbxPackages->id > 0 && $resPbxPackages->id < 10) {
            $packages_number = $prefix->value.'-00000'.$resPbxPackages->id;
        }
        if ($resPbxPackages->id > 9 && $resPbxPackages->id < 100) {
            $packages_number = $prefix->value.'-0000'.$resPbxPackages->id;
        }
        if ($resPbxPackages->id > 99 && $resPbxPackages->id < 1000) {
            $packages_number = $prefix->value.'-000'.$resPbxPackages->id;
        }
        if ($resPbxPackages->id > 999 && $resPbxPackages->id < 10000) {
            $packages_number = $prefix->value.'-00'.$resPbxPackages->id;
        }
        if ($resPbxPackages->id > 9999 && $resPbxPackages->id < 100000) {
            $packages_number = $prefix->value.'-0'.$resPbxPackages->id;
        }
        if ($resPbxPackages->id > 99999) {
            $packages_number = $prefix->value.'-'.$resPbxPackages->id;
        }
        $resPbxPackages->packages_number = $packages_number;

        $resPbxPackages->inclusive_minutes_seconds = $resPbxPackages->inclusive_minutes * 60;
        $resPbxPackages->save();

        if (! $resPbxPackages) {
            return response()->json([
                'pbxPackages' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }
        if ($pbx_package_id) {
            //guarda impuestos normaless
            foreach ($data['taxes'] as $tax) {

                $tax_base = TaxType::find($tax['id']);

                if ($tax_base != null) {

                    //  $this->addPbxPackageTaxes($tax_base, $tax_base['package_id'], $tax_base['item_id'], $tax_base['package_item_id'], $company_id, $pbx_package_id);

                    $this->addPbxPackageTaxTypes($tax_base, $pbx_package_id, $company_id);
                }
            }

            if (array_key_exists('taxesCdr', $data)) {

                //guarda impuestos cdrs

                if (count($data['taxesCdr']) > 0) {

                    foreach ($data['taxesCdr'] as $taxesCdr) {

                        $tax_base = TaxType::find($taxesCdr['id']);

                        if ($tax_base != null) {

                            $this->addPbxPackageTaxTypesCdrs($tax_base, $pbx_package_id, $company_id);

                        }
                    }

                } else {
                    $data['taxesCdr'];
                }

            } else {
                echo $data;
            }

        }
        // Package Items
        if (count($data['items']) > 0) {

            $this->addPackageItems(collect($data['items']), $pbx_package_id, $company_id);
        }

        // Package Group Items
        if (count($data['item_groups']) > 0) {

            $this->addPackageGroupItems(collect($data['item_groups']), $pbx_package_id, $company_id);
        }
        // $PbxPackageNew = PbxPackages::find($resPbxPackages->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxPackages' => $resPbxPackages,
        ], "message" => "PbxPackagesController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxPackagesController store");
        /////////////////////////////////////////

        return response()->json([
            'pbxPackages' => $resPbxPackages,
            'success' => true,
            'message' => 'PBX Package saved successfully',
        ]);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @param \Crater\Http\Requests\PbxPackagesRequest $request
     * @param  \Crater\Models\PbxPackages  $PbxPackages
     * @return \Illuminate\Http\Response
     */
    public function show($id, PbxPackagesRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resPbxPackage = PbxPackages::with('cdrStatus:id,pbx_packages_id,status')->with('taxTypes')
            ->with('pbxPackagesTaxTypes')
            ->with('taxTypesCdr')->with(['items.taxes', 'customAppRate', 'server'])->with('itemGroups')
            ->select('pbx_packages.*')
            ->find($id);

        if ($resPbxPackage) {
            $resPbxPackage->cdrStatus = $resPbxPackage->cdrStatus->pluck('status');
        }

        $ValidarPbxServices = PbxServices::where("pbx_package_id", "=", $id)->where("status", "!=", "C")->count();
        if ($ValidarPbxServices > 0) {
            $resPbxPackage->bandServices = 1;
        } else {
            $resPbxPackage->bandServices = 0;
        }

        // Load Tax Groups (PbxPackage)
        $package_id = $resPbxPackage->id;
        $taxes_groups = [];

        $tax_groups_ids = DB::table('package_tax_groups')->where("pbx_package_id", $package_id)
                                                     ->where('deleted_at', null)
                                                     ->pluck('tax_group_id');

        foreach ($tax_groups_ids as $key => $tax_group_id) {
            $taxes_per_group = [];
            $taxes_id = DB::table('tax_group')->where("tax_groups_id", $tax_group_id)
            ->where('deleted_at', null)
            ->pluck('taxes_id');

            foreach ($taxes_id as $tax_id) {
                $tax = DB::table('tax_types')->where("id", $tax_id)
                ->where('deleted_at', null)
                ->first();
                array_push($taxes_per_group, $tax);
            }

            $tax_group = DB::table('tax_groups')->where("id", $tax_group_id)
                                            ->where('deleted_at', null)
                                            ->first();

            array_push($taxes_groups, $tax_group);
            $taxes_groups[$key]->tax_groups_tax_types = $taxes_per_group;
        }
        //

        // Load Custom Destinations Groups (PbxPackage)
        $package_id = $resPbxPackage->id;
        $custom_destination_groups = [];

        $prefixrate_groups_ids = DB::table('pbx_packages_prefixrate_groups')->where("pbx_package_id", $package_id)
                                                     ->where('deleted_at', null)
                                                     ->pluck('prefixrate_group_id');

        $custom_destination_groups = DB::table('prefixrate_groups')
                                    ->whereIn("id", $prefixrate_groups_ids)
                                    ->where('deleted_at', null)
                                    ->get();
        //

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxPackage' => $resPbxPackage,
        ], "message" => "PbxPackagesController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxPackagesController show");
        /////////////////////////////////////////

        return response()->json([
            'pbxPackage' => $resPbxPackage,
            'tax_groups' => count($taxes_groups) > 0 ? $taxes_groups : null,
            'custom_destination_groups' => count($custom_destination_groups) > 0 ? $custom_destination_groups : [],
            'message' => 'PBX Package',
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Crater\Http\Requests\PbxPackagesRequest  $request
     * @param  \Crater\Models\PbxPackages  $PbxPackages
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PbxPackagesRequest $request, $id, PbxPackages $PbxPackages)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $company_id = Auth::user()->company_id;
        // $data = $request->validated();
        $PbxPackages = PbxPackages::find($id);
        $PbxPackages->update($request->validated());

        $PbxPackages->cdrStatus()->delete();
        foreach ($request->cdrStatus as $key => $value) {
            $PbxPackages->cdrStatus()->create([
                'pbx_packages_id' => $PbxPackages->id,
                'status' => $value,
            ]);
        }

        if ($request->dropdown_server != null) {
            $PbxPackages->pbx_server_id = $request->dropdown_server;
            $updated = $PbxPackages->save();
        }

        $PbxPackages->inclusive_minutes_seconds = $PbxPackages->inclusive_minutes * 60;
        $PbxPackages->save();

        // Packages Tax Types
        PbxPackageTaxTypes::where('pbx_package_id', $id)->delete();
        if ($request->has('taxes')) {
            if (count($request->taxes) > 0) {
                foreach ($request->taxes as $tax) {
                    $this->addPbxPackageTaxTypes($tax, $id, $company_id);
                }
            }
        }

        // Packages Tax Types cdrs
        PbxPackageTaxTypesCdr::where('pbx_package_id', $id)->delete();
        if ($request->has('taxesCdr')) {
            if (count($request->taxesCdr) > 0) {
                foreach ($request->taxesCdr as $tax) {
                    $this->addPbxPackageTaxTypesCdrs($tax, $id, $company_id);
                }
            }
        }

        if ($updated) {
            // Packages Items
            $this->updatePackageItems(collect($request->items), $request->input('id'), $company_id);

            // Package Item Group
            PbxPackageItemGroup::where('pbx_package_id', $request->input('id'))->delete();
            if (count($request->item_groups) > 0) {
                $this->addPackageGroupItems(collect($request->item_groups), $request->input('id'), $company_id);
            }
        }

        //// update tax groups (Delete and Create)
        $tax_groups_ids = DB::table('package_tax_groups')->where("pbx_package_id", $request['id'])
                                                     ->where('deleted_at', null)
                                                     ->pluck('id');
        // Delete
        if(count($tax_groups_ids) > 0) {
            foreach ($tax_groups_ids as $id) {
                DB::table('package_tax_groups')->where('id', $id)->delete();
            }
        }
        // Create
        if(! is_null($request['tax_groups'])) {
            // Create
            foreach ($request['tax_groups'] as $tax_gp) {
                DB::table('package_tax_groups')->insert([
                    'package_id' => null,
                    'pbx_package_id' => $request['id'],
                    'tax_group_id' => $tax_gp['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        }
        ////

        //// Update custom destinations groups (Delete and Create)
        $custom_destination_groups = DB::table('pbx_packages_prefixrate_groups')->where("pbx_package_id", $request['id'])
                                                     ->where('deleted_at', null)
                                                     ->pluck('id');
        // Delete
        if(count($custom_destination_groups) > 0) {
            foreach ($custom_destination_groups as $id) {
                DB::table('pbx_packages_prefixrate_groups')->where('id', $id)->delete();
            }
        }
        // Create
        if(! is_null($request['custom_destination_groups'])) {
            // Create
            foreach ($request['custom_destination_groups'] as $group) {
                DB::table('pbx_packages_prefixrate_groups')->insert([
                    'pbx_package_id' => $request['id'],
                    'prefixrate_group_id' => $group['id'],
                    'type' => $group['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ]);
            }
        }
        ////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxPackages' => $PbxPackages,
        ], "message" => "PbxPackagesController put"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxPackagesController update");
        /////////////////////////////////////////

        return response()->json([
            'pbxPackages' => $PbxPackages,
            'success' => true,
            'message' => 'PBX Package saved successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Crater\Models\PbxPackages  $PbxPackages
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, PbxPackages $PbxPackages, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxPackagesController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $PbxPackage = PbxPackages::find($id);
        if ($PbxPackage) {
            $Pbxservbicelist = PbxServices::where("pbx_package_id", $PbxPackage->id)->get()->count();

            if ($Pbxservbicelist == 0) {
                $PbxPackage->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The package has active services',
                ]);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => 'No existe el registro',
            ]);
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "soft delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "soft delete success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado con éxito.',
        ]);
    }

    public function getPbxServicesRelation($id, Request $request)
    {
        $pbxServices = PbxServices::with(['user.currency', 'pbxPackage'])
            ->where('pbx_package_id', $id)
            ->where('status', $request->status)
            ->orderBy($request->orderByField, $request->orderBy)
            ->paginate(10);

        return response()->json([
            'pbxServices' => $pbxServices,
            'success' => true,
            'message' => 'PBX Services',
        ]);
    }
}
