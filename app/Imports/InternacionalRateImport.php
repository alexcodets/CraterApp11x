<?php

namespace Crater\Imports;

use Crater\Models\ProfileInternacionalRate;
use Maatwebsite\Excel\Concerns\ToModel;

class InternacionalRateImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new ProfileInternacionalRate([
            'type' => $row[0],
            'prefix' => $row[1],
            'from' => $row[2],
            'to' => $row[3],
            'name' => $row[4],
            'rate_per_minute' => $row[5],
            'category' => $row[6],
            'country' => $row[7],
        ]);
    }
}
