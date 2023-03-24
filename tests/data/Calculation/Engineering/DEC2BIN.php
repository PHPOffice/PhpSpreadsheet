<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['101100101', '357'],
    ['1001', 9, 4],
    ['00001001', 9, 8],
    ['001001', 9, 6.75], // Leading places as a float
    [ExcelError::NAN(), 9, -1], // Leading places negative
    [ExcelError::VALUE(), 9, 'ABC'], // Leading places non-numeric
    ['11110110', '246'],
    [ExcelError::NAN(), '12345'],
    [ExcelError::NAN(), '123456789'],
    ['1111011', '123.45'],
    ['0', '0'],
    [ExcelError::VALUE(), '3579A'], // Invalid decimal
    [ExcelError::VALUE(), true], // ODS accepts boolean, Excel/Gnumeric don't
    [ExcelError::VALUE(), false], // ODS accepts boolean, Excel/Gnumeric don't
    ['1110011100', '-100'], // 2's Complement
    ['1110010101', '-107'], // 2's Complement
    ['1000000000', '-512'], // lowest negative
    ['111111111', '511'], // highest positive
    [ExcelError::NAN(), '512'], // Too large
    [ExcelError::NAN(), '-513'], // Too small
    ['0011', 3, 4],
    [ExcelError::NAN(), 3, 0],
    [ExcelError::NAN(), 3, -1],
    [ExcelError::NAN(), 3, 14],
    [ExcelError::NAN(), 3, 1],
    ['11', 3, 2],
];
