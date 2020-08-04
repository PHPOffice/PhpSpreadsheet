<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    [
        0,
    ],
    [
        0,
        true,
    ],
    [
        false,
        false,
    ],
    [
        'ABC',
        true,
        'ABC',
    ],
    [
        false,
        false,
        'ABC',
    ],
    [
        'ABC',
        true,
        'ABC',
        'XYZ',
    ],
    [
        'XYZ',
        false,
        'ABC',
        'XYZ',
    ],
    [
        ExcelException::NA(),
        ExcelException::NA(),
        'ABC',
        'XYZ',
    ],
];
