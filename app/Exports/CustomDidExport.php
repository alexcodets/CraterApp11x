<?php

namespace Crater\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomDidExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([
            [
                'Prefix' => 130,
                'Rate' => 0.004,
                'Category' => 'Premium'
            ],
            [
                'Prefix' => 131,
                'Rate' => 0.005,
                'Category' => 'Premium 2'
            ],
            [
                'Prefix' => 132,
                'Rate' => 0.006,
                'Category' => 'Premium 3'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Prefix',
            'Rate',
            'Category',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '0.000'
        ];
    }
}
