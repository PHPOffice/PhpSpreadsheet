<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['751', '1357'],
    ['166', '246'],
    ['5349', '12345'],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    [ExcelError::NAN(), '3579'],
    ['44', '54'],
    ['-165', '7777777533'], // 2's Complement
    [ExcelError::NAN(), '37777777770'], // too many digits
    ['536870911', '3777777777'], // highest positive
    ['-536870912', '4000000000'], // lowest negative
];
