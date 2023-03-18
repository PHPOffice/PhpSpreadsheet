<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['653', '01AB'],
    ['125715', 'ABCD'],
    ['366', 'F6'],
    ['35516', '3B4E'],
    ['017', 'F', 3],
    ['221505', '12345'],
    [ExcelError::NAN(), '123456789'],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::NAN(), 'G3579A'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    [ExcelError::NAN(), '-107'],
    ['7777777400', 'FFFFFFFF00'], // 2's Complement
    ['3777777777', '1FFFFFFF'], // highest positive
    [ExcelError::NAN(), '20000000'],
    ['4000000000', 'FFE0000000'], // lowest negative
    [ExcelError::NAN(), 'FFDFFFFFFF'],
    ['00012', 'A', 5],
    [ExcelError::NAN(), 'A', 0],
    [ExcelError::NAN(), 'A', -1],
    [ExcelError::NAN(), 'A', 14],
    [ExcelError::NAN(), 'A', 1],
    ['12', 'A', 2],
];
