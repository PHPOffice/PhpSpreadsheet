<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [5.0, 20, 2],
    [3.0, 52, '4'],
    [ExcelError::VALUE(), 'ABC', 5],
    [ExcelError::VALUE(), 5, 'ABC'],
    [ExcelError::NAN(), 1, -48], // result too large
    [ExcelError::NAN(), 1.1, 2], // first arg must be integer
    [1.0, 4, 2.1], // second arg will be truncated
    [ExcelError::NAN(), 0, 54], // second arg too large
    [0.0, 0, 5],
    [ExcelError::NAN(), -16, 2], // first arg cant be negative
    [4.0, 1, -2], // negative shift permitted
    [4.0, 1, -2.1], // negative shift and (ignored) fraction permitted
    [4.0, 4, null],
    [4.0, 4, false],
    [2.0, 4, true],
    [0.0, null, 4],
    [4.0, 4, false],
    [2.0, 4, true],
    [0.0, false, 4],
    [0.0, true, 4],
    [16.0, true, -4],
    [8000000000.0, 1000000000, -3], // result > 2**32
    [8000000000.0, 16000000000, 1], // argument > 2**32
    [ExcelError::NAN(), 2 ** 50, 1], // argument >= 2**48
    [1.0, 2 ** 47, 47],
];
