<?php

namespace Crater\Http\Controllers\V1\Packages;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PackageDescriptions;
use Crater\Models\PackageGroups;
use Crater\Models\PackageItemGroups;
use Crater\Models\PackageItems;
use Crater\Models\PackageNames;
use Crater\Models\Packages;
use Crater\Models\PackageTaxTypes;
use Crater\Models\Tax;
use Crater\Models\TaxGroups;
use Crater\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;

class PackagesController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "PackagesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $packages = Packages::applyFilters($request->only([
            'name', 'package_number', 'status_payment', 'module', 'qty', 'orderByField', 'orderBy',
        ]))
            ->whereCompany($request->header('company'))
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'packages' => $packages,
        ], "message" => "Packages index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Packages index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Packages", "List", "admin/packages", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'packages' => $packages,
            'packageTotalCount' => Packages::count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackagesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        \Log::debug("antes");
        \Log::debug($request->items);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status_payment' => 'required',
        ]);


        \Log::debug("despues");
        \Log::debug($request->items);


        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'There are some required fields missing.',
            ];

            return json_encode($response);
        }

        //Log::debug($request->input('name'));
        $newPackageName = PackageNames::firstWhere('name', $request->input('name'));

        if ($newPackageName) {
            $response = [
                'status' => 200,
                'nameExists' => true,
                'response' => 'The Package name is already in use',
            ];

            return response()->json($response, $response['status']);
        }

        $company_id = Auth::user()->company_id;
        $lang = CompanySetting::getSetting('language', $company_id);

        $newPackage = new Packages();

        if ($request->has('value_discount') || $request->has('discounts') || $request->has('packages_discount')) {
            $newPackage->type = $request->discounts['value'];
            $newPackage->discount = $request->input('value_discount');
            $newPackage->discount_general_type = $request->input('discount_general_type');
            $newPackage->discount_general = $request->input('discount_general');
            $newPackage->packages_discount = $request->input('packages_discount');
        }

        $newPackage->name = $request->input('name');
        $newPackage->status = $request->input('status');
        $newPackage->status_payment = $request->input('status_payment');
        $newPackage->qty = $request->input('qty');
        $newPackage->company_id = $company_id;
        $newPackage->client_qty = $request->input('client_qty');
        $newPackage->upgrades_use_renewal = $request->input('upgrades_use_renewal');
        $newPackage->apply_tax_type = $request->input('apply_tax_type');
        $saved = $newPackage->save();

        $PackageNew = Packages::find($newPackage->id);

        $PackageNew->update(['package_number' => 'PACK-00000'.$newPackage->id]);

        /*if (count($request->groupLeft) > 0) {
        $this->addGroups(collect($request->groupLeft), $newPackage->id, $company_id);
        } */

        if ($saved) {
            $newPackageName = new PackageNames();
            $newPackageName->package_id = $newPackage->id;
            $newPackageName->name = $request->input('name');
            $newPackageName->lang = $lang;
            $newPackageName->company_id = $company_id;

            $newPackageDescription = new PackageDescriptions();
            $newPackageDescription->package_id = $newPackage->id;
            $newPackageDescription->html = $request->input('descriptionHTML');
            $newPackageDescription->text = $request->input('descriptionText');
            $newPackageDescription->company_id = $company_id;
            $newPackageDescription->lang = $lang;

            // Packages Tax Types
            if (count($request->taxes) > 0) {
                $this->addPackageTaxTypes(collect($request->taxes), $newPackage->id, $company_id);
            }

            // Package Items
            if (count($request->items) > 0) {

                $this->addPackageItems(collect($request->items), $newPackage->id, $company_id);
            }

            // Package Group Items
            if (count($request->item_groups) > 0) {

                $this->addPackageGroupItems(collect($request->item_groups), $newPackage->id, $company_id);
            }

            /*if (count($request->groupLeftTax) > 0) {
            $this->addPackageGroupTaxs(collect($request->groupLeftTax), $newPackage->id, $company_id);
            }*/

            // Insert into package_tax_groups (Grupo de taxes pertenecientes a un package)
            if (! is_null($request['tax_groups'])) {
                if (count($request['tax_groups']) > 0) {
                    foreach ($request['tax_groups'] as $tax_gp) {
                        DB::table('package_tax_groups')->insert([
                            'package_id' => $newPackage->id,
                            'pbx_package_id' => null,
                            'tax_group_id' => $tax_gp['id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }
            }
            //

            if ($newPackageName->save() && $newPackageDescription->save()) {
                $response = [
                    'status' => 200,
                    'response' => 'Package stored correctly',
                ];
            } else {
                $response = [
                    'status' => 406,
                    'response' => 'Error saving Name or Description Package',
                ];
            }
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Package',
            ];
        }
        LogsDev::finishLog($log, $response, $time, 'D', "Packages store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Packages", "Store", "admin/packages", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);

    }

    public function packageGroups(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackagesController", "edit");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        LogsDev::finishLog($log, "N/E", $time, 'D', "packageGroups");

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        return PackageGroups::all();
    }

    public function packageTaxGroups(Request $request)
    {
        // Packages Tax Groups
        return TaxGroups::with('taxGroupsTaxTypes')->get();
    }

    public function destroyItemPackagesGroup(Request $request)
    {
        return DB::table('package_group')
            ->where(['package_groups_id' => $request->packagesGroupId])
            ->where(['packages_id' => $request->packagesId])
            ->delete();
    }

    public function addGroups($array, $packageId, $company_id)
    {
        $res = [];

        $array->each(function ($item) use ($packageId, $res, $company_id) {

            $PackageGroup = PackageGroups::where('name', $item['name'])->first();

            if (empty($PackageGroup)) {
                $PackageGroup = PackageGroups::create([
                    'name' => $item['name'],
                    'company_id' => $company_id,
                    'status' => 'A',
                ]);

                $res[] = DB::table('package_group')->insert([
                    'package_groups_id' => $PackageGroup->id,
                    'packages_id' => $packageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {

                $res[] = DB::table('package_group')->insert([
                    'package_groups_id' => $PackageGroup->id,
                    'packages_id' => $packageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return $res;
    }

    public function updateGroups($array, $packageId, $company_id)
    {
        try {

            $res = [];
            DB::table('package_group')->where('packages_id', $packageId)->delete();
            $array->each(function ($item) use ($packageId, $res, $company_id) {

                // if (!empty($item['new'])) {
                $PackageGroup = PackageGroups::where('name', $item['name'])->first();

                if (empty($PackageGroup)) {
                    $PackageGroup = PackageGroups::create([
                        'name' => $item['name'],
                        'company_id' => $company_id,
                        'status' => 'A',
                    ]);

                    $res[] = DB::table('package_group')->insert([
                        'package_groups_id' => $PackageGroup->id,
                        'packages_id' => $packageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $res[] = DB::table('package_group')->insert([
                        'package_groups_id' => $item['id'],
                        'packages_id' => $packageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // }
            });

            return $res;

        } catch (\Throwable $th) {
            Log::debug('error package group', [$th]);
        }
    }

    public function addPackageTaxTypes($taxes, $package_id, $company_id)
    {
        $res = [];
        $taxes->each(function ($item) use ($package_id, $res, $company_id) {
            $newPackageTaxTypes = new PackageTaxTypes();
            $newPackageTaxTypes->package_id = $package_id;
            $newPackageTaxTypes->company_id = $company_id;
            $newPackageTaxTypes->tax_types_id = $item['id'];
            $newPackageTaxTypes->name = $item['name'];
            $newPackageTaxTypes->percent = $item['percent'];
            $newPackageTaxTypes->compound_tax = $item['compound_tax'];
            $res[] = $newPackageTaxTypes->save();
        });

        return $res;
    }

    public function addPackageTaxes($taxes, $package_id, $item_id, $package_item_id, $company_id)
    {
        $res = [];
        $taxes->each(function ($tax) use ($package_id, $res, $item_id, $package_item_id, $company_id) {
            if (! empty($tax['name'])) {
                $newTax = new Tax();
                $newTax->package_id = $package_id;
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

    public function addPackageGroupTaxs($taxs, $package_id, $company_id)
    {
        $res = [];

        $taxs->each(function ($item) use ($package_id, $res) {

            $res[] = DB::table('package_tax_groups')->insert([
                'package_id' => $package_id,
                'tax_group_id' => $item['id'],
                'status' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return $res;
    }

    public function addPackageItems($items, $package_id, $company_id)
    {
        $res = [];
        $items->each(function ($item) use ($package_id, $res, $company_id) {
            \Log::debug($item);
            $newPackageItems = new PackageItems();
            $newPackageItems->package_id = $package_id;
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
            $newPackageItems->end_period_number = $item['end_period_number'];
            $newPackageItems->end_period_act = $item['end_period_act'];

            // Verificar si 'end_period_number' está en null, vacío o no es un número entero
            if (empty($newPackageItems->end_period_number) || ! is_numeric($newPackageItems->end_period_number) || intval($newPackageItems->end_period_number) != $newPackageItems->end_period_number) {
                $newPackageItems->end_period_number = 1;
            }
            $newPackageItems->company_id = $company_id;
            $res[] = $newPackageItems->save();
            if (count($item['taxes']) > 0) {
                $this->addPackageTaxes(collect($item['taxes']), $package_id, $item['item_id'], $newPackageItems->id, $company_id);
            }
        });

        return $res;
    }

    public function addPackageGroupItems($items, $package_id, $company_id)
    {
        $res = [];
        $items->each(function ($item) use ($package_id, $res, $company_id) {
            if ($item['id'] != null) {
                $newPackageItemGroup = new PackageItemGroups();
                $newPackageItemGroup->package_id = $package_id;
                $newPackageItemGroup->company_id = $company_id;
                $newPackageItemGroup->item_group_id = $item['id'];
                $res[] = $newPackageItemGroup->save();
            }
        });

        return $res;
    }

    public function updatePackageItems($items, $package_id, $company_id)
    {
        PackageItems::where('package_id', $package_id)->delete();
        Tax::where('package_id', $package_id)->delete();
        if (count($items) > 0) {
            $this->addPackageItems(collect($items), $package_id, $company_id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackagesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $requestId = ['id' => $id];
        $validator = Validator::make($requestId, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'Id is required.',
            ];

            return response()->json($response, $response['status']);
        }

        $company_id = Auth::user()->company_id;
        $lang = CompanySetting::getSetting('language', $company_id);

        $package = Packages::with('taxTypes')->with('packagesTaxTypes')->with(['items'])->with('itemGroups')
            ->join('package_names', 'packages.id', '=', 'package_names.package_id')
            ->join('package_descriptions', 'packages.id', '=', 'package_descriptions.package_id')
            ->select('packages.*', 'package_names.name as package_name', 'package_descriptions.text', 'package_descriptions.html')
            ->find($id);

        if ($package == null) {
            $response = [
                'status' => 406,
                'response' => 'Package not found',
            ];

            return response()->json($response, $response['status']);
        }

        $package->groupLeft = DB::table('package_group')
            ->join('package_groups', 'package_groups.id', '=', 'package_group.package_groups_id')
            ->where('packages_id', $package->id)
            ->get();

        $package->groupLeftTax = DB::table('package_tax_groups')
            ->join('tax_groups', 'tax_groups.id', '=', 'package_tax_groups.tax_group_id')
            ->where('package_id', $package->id)
            ->get();

        $package->discounts = ['text' => ucfirst($package->type), 'value' => $package->type];
        if (count($package->items) > 0) {
            $package_id = $package->id;
            $package->items->each(function ($item) use ($package_id) {

                $item->taxes = Tax::where('package_item_id', $item->id)->get();

                /* Se agrega esta relacion ya que en la sentencia anterior no esta trayendo los taxes que son agregados
                 * a los items en el modulo de items, los cuales son necesarios cuando se va a agregar un paquete al cliente
                 */
                $item->itemTaxes = Tax::where('item_id', $item->id)
                    ->where('package_id', $package_id)
                    ->whereNull(['invoice_id', 'estimate_id', 'invoice_item_id', 'estimate_item_id', 'pbx_package_id', 'pbx_package_item_id', 'pbx_service_item_id'])
                    ->get();
            });
        }
        // Load Tax Groups (PbxPackage)
        $package_id = $id;
        $taxes_groups = [];

        $tax_groups_ids = DB::table('package_tax_groups')->where("package_id", $package_id)
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

        if ($package) {
            $response = [
                'status' => 200,
                'response' => $package,
                'tax_groups' => count($taxes_groups) > 0 ? $taxes_groups : null,
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Package not found',
            ];
        }
        LogsDev::finishLog($log, $response, $time, 'D', "Packages show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Packages", "Show", "admin/packages/id", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Packages $packages)
    {
        Log::debug('request', [$request->all()]);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PackagesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'status_payment' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'There are some required fields missing.',
            ];

            return response()->json($response, $response['status']);
        }

        $package = Packages::find($request->input('id'));
        if ($package->name != $request->input('name')) {
            $packageName = PackageNames::firstWhere('name', $request->input('name'));

            if ($packageName) {
                $response = [
                    'status' => 406,
                    'response' => 'The Package name is already in use',
                ];

                return response()->json($response, $response['status']);
            }
        }

        if (is_null($package)) {
            $response = [
                'status' => 406,
                'response' => 'Package not found by Id',
            ];

            return response()->json($response, $response['status']);
        } else {
            $company_id = Auth::user()->company_id;
            $lang = CompanySetting::getSetting('language', $company_id);

            $package->name = $request->input('name');
            $package->status = $request->input('status');
            $package->status_payment = $request->input('status_payment');
            $package->qty = $request->input('qty');
            $package->client_qty = $request->input('client_qty');
            $package->upgrades_use_renewal = $request->input('upgrades_use_renewal');
            // $package->packages_discount_none = $request->input('packages_discount_none');
            $package->discount_general_type = $request->input('discount_general_type');
            $package->discount_general = $request->input('discount_general');
            $package->packages_discount = $request->input('packages_discount');
            $package->apply_tax_type = $request->input('apply_tax_type');
            $updated = $package->save();

            if ($updated) {

                if (count($request->groupLeft) > 0) {
                    $this->updateGroups(collect($request->groupLeft), $request->input('id'), $company_id);
                } else {
                    DB::table('package_group')->where('packages_id', $request->input('id'))->delete();
                }

                // Packages Tax Types
                PackageTaxTypes::where('package_id', $request->input('id'))->delete();
                if ($request->has('taxes')) {
                    if (count($request->taxes) > 0) {
                        $this->addPackageTaxTypes(collect($request->taxes), $request->input('id'), $company_id);
                    }
                }

                // Packages Items
                $this->updatePackageItems(collect($request->items), $request->input('id'), $company_id);

                // Package Item Group
                PackageItemGroups::where('package_id', $request->input('id'))->delete();
                if (count($request->item_groups) > 0) {
                    $this->addPackageGroupItems(collect($request->item_groups), $request->input('id'), $company_id);
                }

                $packageNameId = PackageNames::where('package_id', $request->input('id'))
                    ->where('lang', $lang)
                    ->first();
                $packageNameId->name = $request->input('name');
                $packageNameId->lang = $lang;

                $packageDescription = PackageDescriptions::where('package_id', $request->input('id'))
                    ->where('lang', $lang)
                    ->first();
                $packageDescription->html = $request->input('descriptionHTML');
                $packageDescription->text = $request->input('descriptionText');
                $packageDescription->lang = $lang;

                //// update tax groups (Delete and Create)
                $tax_groups_ids = DB::table('package_tax_groups')->where("package_id", $request['id'])
                    ->where('deleted_at', null)
                    ->pluck('id');

                // Delete
                if (count($tax_groups_ids) > 0) {
                    foreach ($tax_groups_ids as $id) {
                        DB::table('package_tax_groups')->where('id', $id)->delete();
                    }
                }
                // Create
                if (! is_null($request['tax_groups'])) {
                    // Create
                    foreach ($request['tax_groups'] as $tax_gp) {
                        DB::table('package_tax_groups')->insert([
                            'package_id' => $request['id'],
                            'pbx_package_id' => null,
                            'tax_group_id' => $tax_gp['id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }
                ////

                if ($packageNameId->save() && $packageDescription->save()) {
                    $response = [
                        'status' => 200,
                        'response' => 'Package update correctly',
                    ];
                } else {
                    $response = [
                        'status' => 406,
                        'response' => 'Error saving Name or Description Package',
                    ];
                }
            } else {
                $response = [
                    'status' => 406,
                    'response' => 'Error saving Package',
                ];
            }
            LogsDev::finishLog($log, $response, $time, 'D', "Packages update");
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

            // Logs por modulo
            LogsModule::createLog("Packages", "Update", "admin/packages/id/edit", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

            return response()->json($response, $response['status']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package_not_found_in_CP = CustomerPackage::with('package')->where('package_id', '=', $id)->get()->count();

        if ($package_not_found_in_CP == 0) {
            $package = Packages::find($id);
            if ($package->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Package deleted successfully',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cannot be removed while associated with Customer Package',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function listItemGroups(Request $request)
    {
        $item_groups = DB::table('item_groups')->whereNull('deleted_at')->get();
        // var_dump($item_groups);
        $item_groups_collec = collect($item_groups);
        $item_groups_collec->each(function ($item) {
            // var_dump($item->id);
            $res = DB::table('item_group_items')
                ->WHERE('item_group_id', $item->id)
                ->join('items', 'items.id', '=', 'item_group_items.item_id')
                ->select('item_group_items.id as item_group_items_id', 'items.*')
                ->whereNull('item_group_items.deleted_at')
                ->get();
            $item->items = $res;
        });

        if ($item_groups) {
            $response = [
                'status' => 200,
                'response' => $item_groups_collec,
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Items Groups not found',
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function listItemGroupsPos(Request $request)
    {
        $item_groups = DB::table('item_groups')->whereNull('deleted_at')->where('allow_pos', 1)->get();
        // var_dump($item_groups);
        $item_groups_collec = collect($item_groups);
        $item_groups_collec->each(function ($item) {
            // var_dump($item->id);
            $res = DB::table('item_group_items')
                ->WHERE('item_group_id', $item->id)
                ->join('items', 'items.id', '=', 'item_group_items.item_id')
                ->select('item_group_items.id as item_group_items_id', 'items.*')
                ->whereNull('item_group_items.deleted_at')
                ->get();
            $item->items = $res;
        });

        if ($item_groups) {
            $response = [
                'status' => 200,
                'response' => $item_groups_collec,
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Items Groups not found',
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackagesByGroups(Request $request)
    {
        $customer = User::findOrfail($request->customer_id);

        $data = Packages::where('packages.company_id', $request->header('company'))
            ->where('packages.status', 'A')
            ->whereNull('packages.deleted_at')
            ->whereNull('package_groups.deleted_at')
            ->when($customer->status_payment == 'prepaid', function ($q) {
                return $q->where('packages.status_payment', 'R');
            })
            ->when($customer->status_payment == 'postpaid', function ($q) {
                return $q->where('packages.status_payment', 'O');
            })
            ->leftJoin('package_group', 'packages.id', 'package_group.packages_id')
            ->leftJoin('package_groups', 'package_group.package_groups_id', 'package_groups.id')
            ->select(
                'package_groups.name as package_group_name',
                'packages.id as package_id',
                'packages.name as package_name'
            )
            ->get()
            ->groupBy('package_group_name');

        return response()->json([
            'packagesByGroup' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackagesServices(Request $request, $id)
    {
        try {
            $customer = Packages::findOrFail($id);
            $time = microtime(true);

            // Init log
            $log = LogsDev::initLog($request, "", "D", "PackagesController", "getPackagesServices");

            $limit = $request->input('limit', 10);
            //Log::debug( $customer);
            //Log::debug( $request->status);
            //Log::debug( $request->header('company'));

            $packagesList = CustomerPackage::where('company_id', $request->header('company'))
                ->where('package_id', $customer->id)
                ->where('status', $request->status)
                ->with(['package', 'user'])
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'packagesList' => $packagesList,
                        'success' => true,
                    ],
                    "message" => "Lista de paquetes asociados a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de paquetes asociados a un cliente");

            // Module log
            LogsModule::createLog(
                "Customers",
                "Get Packages",
                "admin/customers/:id/packages",
                0,
                Auth::user()->name,
                Auth::user()->email,
                Auth::user()->role,
                Auth::user()->company_id
            );

            return response()->json([
                'packagesList' => $packagesList,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
