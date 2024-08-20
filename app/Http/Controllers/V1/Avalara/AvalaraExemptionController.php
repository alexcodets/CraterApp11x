<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\AvalaraExemptionRequest;
use Crater\Models\AvalaraExemption;
use Crater\Models\User;
use Illuminate\Http\Request;
use Log;

class AvalaraExemptionController extends Controller
{
    public function index(Request $request, $user_id)
    {
        /** @var User $user */
        $user = User::find($user_id);
        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'User does not exist', 'data' => '']);
        }
        if (isset($request->avalara_locations_id)) {
            $exemptions = $user->exemptions->filter(function ($item) use ($request) {
                return $item->avalara_locations_id == $request->avalara_locations_id;
            })->values();
        } else {
            $exemptions = $user->exemptions;
        }

        return [
            'success' => true,
            'data' => $exemptions,
        ];
    }

    public function store(AvalaraExemptionRequest $request, $user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'User does not exist', 'data' => '']);
        }

        //Log::debug($request->validated());
        $user->exemptions()->create($request->validated());

        return response()->json([
            'message' => 'success',
            'success' => true,
            'data' => [],
        ]);
    }

    public function update(AvalaraExemptionRequest $request, $user_id, $exemption_id)
    {

        $user = User::whereHas('exemptions', function ($query) use ($exemption_id) {
            $query->where('id', $exemption_id);
        })->where('id', $user_id);

        if (is_null($user)) {
            return response()->json(['success' => false, 'message' => 'User or Exemption does not exist', 'data' => '']);
        }

        AvalaraExemption::where('id', $exemption_id)->update($request->validated());

        return response()->json([
            'message' => 'AvalaraExemption Update',
            'success' => true,
            'data' => [],
        ]);

    }
}
