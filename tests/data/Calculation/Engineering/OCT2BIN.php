<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [ExcelError::NAN(), '1357'],
    ['10100110', '246'],
    ['011', 3, 3],
    [ExcelError::NAN(), '12345'],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    [ExcelError::NAN(), '3579'],
    ['1000000000', '7777777000'], // 2's Complement
    ['1111111111', '7777777777'], // 2's Complement
    [ExcelError::NAN(), '17777777777'], // Too small
    [ExcelError::NAN(), '1777'], // Too large
    ['111111111', '777'], // highest positive
    [ExcelError::NAN(), '1000'],
    ['1000000000', '7777777000'], // lowest negative
    [ExcelError::NAN(), '7777776777'],
    ['01010', 12, 5],
    [ExcelError::NAN(), 12, 0],
    [ExcelError::NAN(), 12, -1],
    [ExcelError::NAN(), 12, 14],
    [ExcelError::NAN(), 12, 3],
    ['1010', 12, 4],
];
