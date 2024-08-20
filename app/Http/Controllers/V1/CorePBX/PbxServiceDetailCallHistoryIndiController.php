<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\CallHistoryIndi;
use Crater\Models\PbxServices;

class PbxServiceDetailCallHistoryIndiController extends Controller
{
    public function index(PbxServices $pbxService)
    {
        $ids = $pbxService->pbxCdrTotals()->get('id')->pluck('id');
        $idsnull = $pbxService->pbxCdrTotals()->whereNULL("invoice_id")->get('id')->pluck('id');
        $limit = 10;
        $response = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $ids)->with('pbxCdrTotal')->orderData('history_call_indi', 'created_at', 'desc')->paginateData($limit);
        foreach ($response as $res) {
            $res->name = $res->pbxCdrTotal->number;
            $res->type = $res->pbxCdrTotal->typeCall;
            $res->setAppends(['date']);
            $res->setHidden(['call_detail_register_totals_id', 'created_at', 'updated_at', 'deleted_at']);
            $res->amout = number_format($res->amout, 5, '.', '');
            $res->taxamount = number_format($res->taxamount, 5, '.', '');
            unset($res->pbxCdrTotal);
        }

        $all = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $ids);
        $current = CallHistoryIndi::where("amout", "!=", 0)->WhereIn('call_detail_register_totals_id', $idsnull);

        return response()->json([
            'prepaid_charges' => $response,
            'totals' => [
                'all' => ['amout' => $all->sum('amout'), 'taxamount' => $all->sum('taxamount')],
                'current' => ['amout' => $current->sum('amout'), 'taxamount' => $current->sum('taxamount')],
            ],
        ]);

        //current, que se traiga el total de cada uno para los registros de call_history no facturados
        //all; que se traiga el total de cada uno para los registros de call_history facturados o no,  es decir todos

    }
}
