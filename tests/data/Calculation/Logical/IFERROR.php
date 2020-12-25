<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    [
        null,
        null,
        'Error',
    ],
    [
        true,
        true,
        'Error',
    ],
    [
        42,
        42,
        'Error',
    ],
    [
        '',
        '',
        'Error',
    ],
    [
        'ABC',
        'ABC',
        'Error',
    ],
    [
        'Error',
        ExcelException::VALUE(),
        'Error',
    ],
    [
        'Error',
        ExcelException::NAME(),
        'Error',
    ],
    [
        'Error',
        ExcelException::NA(),
        'Error',
    ],
];
