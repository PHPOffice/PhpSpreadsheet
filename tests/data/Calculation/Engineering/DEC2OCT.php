<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['545', '357'],
    ['2515', '1357'],
    ['366', '246'],
    ['30071', '12345'],
    ['726746425', '123456789'],
    ['173', '123.45'],
    ['072', 58, 3],
    ['0', '0'],
    [ExcelError::VALUE(), '3579A'], // Invalid decimal
    [ExcelError::VALUE(), true], // ODS accepts bool, Excel/Gnumeric do not
    [ExcelError::VALUE(), false],
    ['7777777634', '-100'], // 2's Complement
    ['7777777625', '-107'], // 2's Complement
    ['3777777777', 536870911], // highest positive
    [ExcelError::NAN(), 536870912],
    ['4000000000', -536870912], // lowest negative
    [ExcelError::NAN(), -536870913],
    ['0403', 259, 4],
    [ExcelError::NAN(), 259, 0],
    [ExcelError::NAN(), 259, -1],
    [ExcelError::NAN(), 259, 14],
    [ExcelError::NAN(), 259, 1],
    ['403', 259, 3],
];
