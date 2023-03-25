<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [50, 21, 39],
    [112, 200, '184'],
    [216, 114.00, 170.00],
    [ExcelError::VALUE(), 'ABC', 'DEF'],
    [ExcelError::VALUE(), 'ABC', 1],
    [ExcelError::VALUE(), 1, 'DEF'],
    [ExcelError::NAN(), 12.00, 2.82E14],
    [5123456789, 5123456788, 1],
    [2583096320, 5123456789, 7123456789],
    [ExcelError::NAN(), -5123456788, 1],
    [ExcelError::NAN(), 2 ** 50, 1], // argument >= 2**48
    [ExcelError::NAN(), 1, 2 ** 50], // argument >= 2**48
    [ExcelError::NAN(), -2, 1], // negative argument
    [ExcelError::NAN(), 2, -1], // negative argument
    [ExcelError::NAN(), -2, -1], // negative argument
    [ExcelError::NAN(), 3.1, 1], // non-integer argument
    [ExcelError::NAN(), 3, 1.1], // non-integer argument
    [4, 4, null],
    [4, 4, false],
    [5, 4, true],
    [4, null, 4],
    [4, false, 4],
    [5, true, 4],
];
