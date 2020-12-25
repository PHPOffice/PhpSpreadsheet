<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

// fractional_dollar, fraction, result

return [
    [
        1.125,
        1.02,
        16,
    ],
    [
        1.3125,
        1.1000000000000001,
        32,
    ],
    [
        1.0625,
        1.01,
        16,
    ],
    [
        1.625,
        1.1000000000000001,
        16,
    ],
    [
        1.09375,
        1.03,
        32,
    ],
    [
        1.9375,
        1.3,
        32,
    ],
    [
        1.375,
        1.1200000000000001,
        32,
    ],
    [
        ExcelException::DIV0(),
        1.2344999999999999,
        0,
    ],
    [
        ExcelException::NUM(),
        1.2344999999999999,
        -2,
    ],
];
