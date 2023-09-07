<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['427', '01AB'],
    ['43981', 'ABCD'],
    ['246', 'F6'],
    ['74565', '12345'],
    ['4886718345', '123456789'],
    [ExcelError::NAN(), '123.45'],
    ['0', '0'],
    [ExcelError::NAN(), 'G3579A'],
    [ExcelError::VALUE(), true],
    [ExcelError::VALUE(), false],
    [ExcelError::NAN(), '-107'],
    ['165', 'A5'],
    ['1034160313', '3DA408B9'],
    ['-165', 'FFFFFFFF5B'], // 2's Complement
    ['-1', 'FFFFFFFFFF'], // 2's Complement
    [ExcelError::NAN(), '1FFFFFFFFFF'], // Too large
    ['549755813887', '7fffffffff'], // highest positive, succeeds even for 32-bit
    ['-549755813888', '8000000000'], // lowest negative, succeeds even for 32-bit
    ['-2147483648', 'ff80000000'],
    ['2147483648', '80000000'],
    ['2147483647', '7fffffff'],
];
