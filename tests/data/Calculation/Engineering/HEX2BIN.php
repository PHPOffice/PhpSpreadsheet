<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['11111111', 'FF'],
    ['111111111', '1FF'],
    [ExcelError::NAN(), '200'],
    ['1000000000', 'FFFFFFFE00'], // 2's Complement
    [ExcelError::NAN(), 'FFFFFFFDFF'], // 2's Complement
    ['111111111', '01FF'], // highest positive
    [ExcelError::NAN(), '0200'],
    ['1000000000', 'FFFFFFFE00'], // lowest negative
    [ExcelError::NAN(), 'FFFFFFFDFF'],
    ['110101011', '01AB'],
    [ExcelError::NAN(), 'ABCD'],
    ['11110110', 'F6'],
    ['00001111', 'F', 8],
    ['10110111', 'B7'],
    [ExcelError::NAN(), '12345'],
    [ExcelError::NAN(), '123456789'],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::NAN(), 'G3579A'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    ['01010', 'A', 5],
    [ExcelError::NAN(), 'A', 0],
    [ExcelError::NAN(), 'A', -1],
    [ExcelError::NAN(), 'A', 14],
    [ExcelError::NAN(), 'A', 3],
    ['1010', 'A', 4],
];
