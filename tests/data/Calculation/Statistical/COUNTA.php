<?php

use PhpOffice\PhpSpreadsheet\Shared\Date;

return [
    [
        5,
        [-1, 0, 1, 2, 3],
    ],
    'null treated differently references vs. direct' => [
        [11, 14],
        [
            // The index simulates a cell value
            '0.1.A' => Date::stringToExcel('1900-02-01'),
            '0.2.A' => 0,
            '0.3.A' => null,
            '0.4.A' => 1.2,
            '0.5.A' => '',
            '0.6.A' => 2.4,
            '0.7.A' => null,
            '0.8.A' => '',
            '0.9.A' => 3.6,
            '0.10.A' => null,
            '0.11.A' => '',
            '0.12.A' => 4.8,
            '0.13.A' => 'Not a numeric',
            '0.14.A' => 6,
        ],
    ],
];
