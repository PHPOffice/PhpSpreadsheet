<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    [
        ExcelException::VALUE(),
        'ABC',
    ],
    [
        -0.10033534773108,
        -10,
    ],
    [
        -0.16051955750789,
        -M_PI * 2,
    ],
    [
        -0.20273255405408,
        -5,
    ],
    [
        -0.32976531495670,
        -M_PI,
    ],
    [
        -0.75246926714193,
        -M_PI / 2,
    ],
    [
        ExcelException::NUM(),
        -0.1,
    ],
    [
        ExcelException::NUM(),
        0.0,
    ],
    [
        ExcelException::NUM(),
        0.1,
    ],
    [
        0.75246926714193,
        M_PI / 2,
    ],
    [
        0.32976531495670,
        M_PI,
    ],
    [
        0.20273255405408,
        5,
    ],
    [
        0.16051955750789,
        M_PI * 2,
    ],
    [
        0.10033534773108,
        10,
    ],
];
