<?php

namespace Crater\Exports;

use Crater\Models\ProfileInternacionalRate;
use Maatwebsite\Excel\Concerns\FromCollection;

class InternacionalRateExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProfileInternacionalRate::all();
    }
}
