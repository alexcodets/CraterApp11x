<?php

namespace Crater\DataObject;

class CdrDO
{
    public function getCdr(array $array)
    {
        if (count($array) == 8) {
            return $this->billedMode($array);
        }

        return $this->unBilledMode($array);
    }

    public function billedMode(array $cdr): array
    {
        return [
            'from' => $cdr[0],
            'to' => $cdr[1],
            'start_date' => $cdr[2],
            'duration' => $cdr[3],
            'billing_duration' => $cdr[4],
            'cost' => $cdr[5],
            'status' => $cdr[6],
            'unique_id' => $cdr[7],
        ];
    }

    public function unBilledMode(array $cdr): array
    {
        return [
            'from' => $cdr[0],
            'to' => $cdr[1],
            'start_date' => $cdr[2],
            'duration' => $cdr[3],
            'billing_duration' => $cdr[3],
            'cost' => null,
            'status' => $cdr[4],
            'unique_id' => $cdr[5],
        ];
    }
}
