<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    [
        1,
        0,
    ],
    [
        48,
        6,
    ],
    [
        105,
        7,
    ],
    [
        15,
        5,
    ],
    [
        384,
        8,
    ],
    [
        135135,
        13,
    ],
    [
        ExcelException::NUM(),
        -1,
    ],
    [
        ExcelException::VALUE(),
        'ABC',
    ],
];
