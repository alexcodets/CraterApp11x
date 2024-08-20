<?php

namespace Crater\Avalara\DataObject;

use Illuminate\Support\Facades\Log;

class AvalaraTaxResponseDO
{
    public function getData(array $values): array
    {
        //Log::debug("Unfiltered Avalara Taxes Response-----------");

        $values['data']['doc'] = $values['data']['inv'][0]['doc'] ?? null;
        $values['data']['items'] = $values['data']['inv'][0]['itms'];
        $values['data']['report_info'] = $values['data']['inv'][0]['incrf'] ?? null;
        unset($values['data']['inv']);

        foreach ($values['data']['items'] as $index => $item) {
            $values['data']['items'][$index]['total']['tax'] = 0;
            $values['data']['items'][$index]['total']['items'] = 0;
            foreach ($item['txs'] as $tx) {
                $values['data']['items'][$index]['total']['tax'] += $tx['tax'];
                $values['data']['items'][$index]['total']['items']++;
            }
        }

        return $values;

    }
}
