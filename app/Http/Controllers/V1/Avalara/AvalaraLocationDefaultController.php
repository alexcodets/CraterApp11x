<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraLocation;
use Crater\Models\Company;
use Crater\Models\User;

class AvalaraLocationDefaultController extends Controller
{
    public function user($location_id)
    {
        $avalaraLocation = AvalaraLocation::find($location_id);
        if (is_null($avalaraLocation)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Id',
            ]);

        }
        // set Default
        $success = User::where('id', $avalaraLocation->user_id)->update(['avalara_location_id' => $avalaraLocation->id]);

        if ($success == false) {
            return response()->json([
                'success' => false,
                'message' => 'A not expected error just happened',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function company($location_id)
    {
        $avalaraLocation = AvalaraLocation::find($location_id);
        if (is_null($avalaraLocation)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Id',
            ]);

        }
        // set Default
        $success = Company::where('id', $avalaraLocation->company_id)->update(['avalara_location_id' => $avalaraLocation->id]);

        if ($success == false) {
            return response()->json([
                'success' => false,
                'message' => 'A not expected error just happened',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }
}
