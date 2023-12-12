<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Shared\Date;

return [
    [
        5,
        [-1, 0, 1, 2, 3],
    ],
    [
        7,
        [
            Date::stringToExcel('1900-02-01'),
            0,
            null,
            1.2,
            '',
            2.4,
            '',
            3.6,
            '',
            4.8,
            'Not a numeric',
            6,
        ],
    ],
];
