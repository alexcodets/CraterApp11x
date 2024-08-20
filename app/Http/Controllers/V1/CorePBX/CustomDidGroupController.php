<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Carbon\Carbon;
use Crater\Exports\CustomDidExport;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomDidGroupRequest;
use Crater\Imports\CustomDidImport;
use Crater\Models\CustomDidGroup;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PbxCategorie;
use Crater\Models\ProfileDidCustomDidGroups;
use Crater\Models\ProfileDidTollFree;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class CustomDidGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "CustomDidGroupController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $customDidGroups = CustomDidGroup::whereCompany($request->header('company'))
            ->applyFilters(
                $request->only([
                    'name',
                    'description',
                    'orderByField',
                    'status',
                    'type',
                    'orderBy',
                ])
            )

            ->with('customDids')
            ->paginateData($limit);

        $count = CustomDidGroup::whereCompany($request->header('company'))->count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customDidGroups' => $customDidGroups,
                    'customDidGroupsTotalCount' => $count,
                ],
                "message" => "Custom did groups list",
            ],
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "End Custom did groups list");

        return response()->json([
            'customDidGroups' => $customDidGroups,
            'customDidGroupsTotalCount' => $count,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CustomDidGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustomDidGroupRequest $request)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomDidGroupController", "store");

        $customDidGroup = CustomDidGroup::createCustomDidGroup($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customDidGroup' => $customDidGroup,
                    'success' => true,
                ],
                "message" => "Custom did group save",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End custom did group save");

        // Module log
        LogsModule::createLog(
            "Custom Did Groups",
            "Create",
            "/admin/corePBX/billing-templates/custom-did-groups/create",
            $customDidGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Custom Did Group: ".$customDidGroup->name
        );

        return response()->json([
            'customDidGroup' => $customDidGroup,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param CustomDidGroup $customDidGroup
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, CustomDidGroup $customDidGroup)
    {
        $time = microtime(true);
        $request->merge(['customDidGroup' => $customDidGroup]);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomDidGroupController", "show");

        $customDidGroup->load('customDids');

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customDidGroup' => $customDidGroup,
                ],
                "message" => "Custom did group show",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End custom did group show");

        // Module log
        LogsModule::createLog(
            "Custom did groups",
            "show",
            "/admin/corePBX/billing-templates/custom-did-groups/:id/view",
            $customDidGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Custom Did Group: ".$customDidGroup->name
        );

        return response()->json([
            'customDidGroup' => $customDidGroup,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CustomDidGroupRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomDidGroupRequest $request, CustomDidGroup $customDidGroup)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomDidGroupController", "update");

        // Update custom did group
        $itemGroup = CustomDidGroup::updateCustomDidGroup($request, $customDidGroup);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customDidGroup' => $customDidGroup,
                    'success' => true,
                ],
                "message" => "Custom did group update",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "End custom did group update");

        // Module log
        LogsModule::createLog(
            "Custom did groups",
            "show",
            "/admin/corePBX/billing-templates/custom-did-groups/:id/edit",
            $customDidGroup->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "Custom Did Group: ".$customDidGroup->name
        );

        return response()->json([
            'customDidGroup' => $customDidGroup,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        // Log init
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "CustomDidGroupController", "delete");

        $profileDidCustomGroups = ProfileDidCustomDidGroups::where('custom_did_group_id', $request->id)->get()->count();

        if ($profileDidCustomGroups == 0) {
            $CustomDidGroup = CustomDidGroup::find($request->id);
            $CustomDidGroup->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'It cannot be removed, it is related to a did template',
            ]);
        }

        // res Log
        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Custom did group delete",
            ],
        ];

        // Log finish
        LogsDev::finishLog($log, $res, $time, 'D', "End custom did group delete");

        return response()->json([
            'success' => true,
            'message' => 'Custom DID Group deleted successfully',
        ]);
    }

    public function importParse(Request $request)
    {
        $path = $request->file('import_data');

        $headings = (new HeadingRowImport())->toArray($path);
        $data = Excel::toArray(new CustomDidImport(), $path)[0];
        $view_data = [];

        if (count($data) > 0) {
            $view_data = array_slice($data, 0, 10);
        }

        return response()->json([
            'header' => $headings[0][0],
            'db_fields' => CustomDidGroup::DB_FIELDS,
            'view_data' => $view_data,
            'success' => true,
        ]);
    }

    public function importProcess(Request $request)
    {
        $form_data = json_decode($request->formData);
        $paired_columns = json_decode($request->paired_columns);

        $reader = Excel::toArray(new CustomDidImport(), $request->file('import_data'))[0];

        $custom_dids = [];

        foreach ($reader as $row) {
            if (! empty($row[$paired_columns->prefijo]) && ! empty($row[$paired_columns->rate_per_minute])
                && is_numeric($row[$paired_columns->rate_per_minute]) && ! empty($row[$paired_columns->toll_free_category_id])) {
                $category = PbxCategorie::firstOrCreate(
                    ['name' => $row[$paired_columns->toll_free_category_id]],
                    ['company_id' => $request->header('company')]
                );

                array_push(
                    $custom_dids,
                    [
                        'prefijo' => $row[$paired_columns->prefijo],
                        'rate_per_minute' => $row[$paired_columns->rate_per_minute],
                        'toll_free_category_id' => $category->id,
                        'company_id' => $request->header('company'),
                        'creator_id' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        if (ProfileDidTollFree::insert($custom_dids)) {
            $lastId = ProfileDidTollFree::orderByDesc('id')->first()->id;
            $ids = range($lastId - count($custom_dids) + 1, $lastId);
            $this->assignCustomDidByImport($form_data, $ids, $request);

            return response()->json([
                'success' => true,
            ]);
        }

        return false;
    }

    public function assignCustomDidByImport($form_data, $did_ids, $request)
    {
        if ($form_data->type_group == 'existing_group') {
            $custom_did_group = CustomDidGroup::find($form_data->prefixrate_groups_id->id);
        } else {
            $custom_did_group = CustomDidGroup::create([
                'name' => $form_data->name,
                'type' => $form_data->type->value,
                'company_id' => $request->header('company'),
            ]);
        }

        foreach ($did_ids as $did) {
            $custom_did_group->customDids()
                ->attach(
                    $did,
                    [
                        'company_id' => $request->header('company'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
        }
    }

    public function exportProcess(Request $request)
    {
        return Excel::download(new CustomDidExport(), 'CustomDid.xlsx');
    }
}
