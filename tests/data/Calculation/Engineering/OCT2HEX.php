<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['2EF', '1357'],
    ['A6', '246'],
    ['14E5', '12345'],
    ['0040', 100, 4],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    [ExcelError::NAN(), '3579'],
    ['FFFFFFFF5B', '7777777533'], // 2's Complement
    ['00108', 410, 5],
    [ExcelError::NAN(), 410, 0],
    [ExcelError::NAN(), 410, -1],
    [ExcelError::NAN(), 410, 14],
    [ExcelError::NAN(), 410, 2],
    ['108', 410, 3],
    [ExcelError::NAN(), '37777777770'], // too many digits
    ['1FFFFFFF', '3777777777'], // highest positive
    ['FFE0000000', '4000000000'], // lowest negative
];
