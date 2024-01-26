<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['B2', 10110010],
    ['B2', '10110010'],
    [ExcelError::NAN(), '111001010101'], // Too large
    ['00FB', '11111011', 4], // Leading places
    ['0FB', '11111011', 3.75], // Leading places as a float
    [ExcelError::NAN(), '11111011', -1], // Leading places negative
    [ExcelError::VALUE(), '11111011', 'ABC'], // Leading places non-numeric
    ['E', '1110'],
    ['5', '101'],
    ['2', '10'],
    ['0', '0'],
    [ExcelError::NAN(), '21'], // Invalid binary number
    [ExcelError::VALUE(), true], // ODS accepts Boolean, Excel/Gnumeric don't
    [ExcelError::VALUE(), false], // ODS accepts Boolean, Excel/Gnumeric don't
    ['FFFFFFFF95', '1110010101'], // 2's Complement
    ['FFFFFFFFFF', '1111111111'], // 2's Complement
    ['FFFFFFFE00', '1000000000'], // lowest negative
    ['1FF', '111111111'], // highest positive
    ['0', '0000000000'],
    ['1', '000000001'],
    ['100', '0100000000'],
    ['100', '100000000'],
    ['FFFFFFFF00', '1100000000'],
    ['0003', '11', 4],
    [ExcelError::NAN(), '11', 0],
    [ExcelError::NAN(), '11', -1],
    [ExcelError::NAN(), '11', 14],
    [ExcelError::NAN(), '10001', 1],
    ['11', '10001', '2'],
    ['011', '10001', '3'],
];
