<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraExemption;
use Crater\Models\User;

class AvalaraExemptionEnableController extends Controller
{
    public function enable(int $user_id, int $exemption_id)
    {
        if (User::where('id', $user_id)->doesntExist()) {
            return [
                'success' => false,
                'message' => 'Invalid User Id',
            ];
        }

        if (AvalaraExemption::where('id', $exemption_id)->where('user_id', $user_id)->doesntExist()) {
            return [
                'success' => false,
                'message' => 'Invalid Avalara Exemption',
            ];
        }

        AvalaraExemption::where(['id' => $exemption_id, 'user_id' => $user_id])->update(['enable' => true]);

        return [
            'success' => true,
            'message' => 'Exemption Enable'
        ];
    }

    public function disable(int $user_id, int $exemption_id)
    {
        if (User::where('id', $user_id)->doesntExist()) {
            return [
                'success' => false,
                'message' => 'Invalid User Id',
            ];
        }

        if (AvalaraExemption::where('id', $exemption_id)->where('user_id', $user_id)->doesntExist()) {
            return [
                'success' => false,
                'message' => 'Invalid Avalara Exemption',
            ];
        }

        AvalaraExemption::where(['id' => $exemption_id, 'user_id' => $user_id])->update(['enable' => false]);

        return [
            'success' => true,
            'message' => 'Exemption Disable'
        ];
    }
}
