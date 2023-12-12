<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [5, 21, 39],
    [64, 200, '84'],
    [8, 72.00, 184.00],
    [ExcelError::VALUE(), 'ABC', 'DEF'],
    [ExcelError::VALUE(), 1, 'DEF'],
    [ExcelError::VALUE(), 'ABC', 1],
    [ExcelError::NAN(), 12.00, 2.82E14],
    [5123456789, 5123456789, 5123456789],
    [4831908629, 5123456789, 7123456789],
    [21, 5123456789, 31],
    [ExcelError::NAN(), -5123456788, 1],
    [ExcelError::NAN(), 2 ** 50, 1], // argument >= 2**48
    [ExcelError::NAN(), 1, 2 ** 50], // argument >= 2**48
    [ExcelError::NAN(), -2, 1], // negative argument
    [ExcelError::NAN(), 2, -1], // negative argument
    [ExcelError::NAN(), -2, -1], // negative argument
    [ExcelError::NAN(), 3.1, 1], // non-integer argument
    [ExcelError::NAN(), 3, 1.1], // non-integer argument
    [0, 4, null],
    [0, 4, false],
    [1, 3, true],
    [0, null, 4],
    [0, false, 4],
    [1, true, 5],
];
