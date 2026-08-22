<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['165', '357'],
    ['54D', '1357'],
    ['F6', '246'],
    ['3039', '12345'],
    ['75BCD15', '123456789'],
    ['0064', 100, 4],
    ['00064', 100, 5.75], // Leading places as a float
    [ExcelError::NAN(), 100, -1], // Leading places negative
    [ExcelError::VALUE(), 100, 'ABC'], // Leading places non-numeric
    ['7B', '123.45'],
    ['0', '0'],
    [ExcelError::VALUE(), '3579A'], // Invalid decimal
    [ExcelError::VALUE(), true], // ODS accepts boolean, Excel/Gnumeric don't
    [ExcelError::VALUE(), false],
    ['FFFFFFFFCA', '-54'], // 2's Complement
    ['FFFFFFFF95', '-107'], // 2's Complement
    ['FF80000001', '-2147483647'], // 2's Complement
    ['FF80000000', '-2147483648'], // 2's Complement
    ['7FFFFFFFFF', 549755813887], // highest positive, succeeds even for 32-bit
    [ExcelError::NAN(), 549755813888],
    ['8000000000', -549755813888], // lowest negative, succeeds even for 32-bit
    ['A2DE246000', -400000000000],
    ['5D21DBA000', 400000000000],
    [ExcelError::NAN(), -549755813889],
    ['0103', 259, 4],
    [ExcelError::NAN(), 259, 0],
    [ExcelError::NAN(), 259, -1],
    [ExcelError::NAN(), 259, 14],
    [ExcelError::NAN(), 259, 1],
    ['103', 259, 3],
];
