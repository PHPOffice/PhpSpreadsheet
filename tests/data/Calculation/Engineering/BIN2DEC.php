<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    ['178', 10110010],
    ['178', '10110010'],
    ['100', '1100100'],
    [ExcelError::NAN(), '111001010101'], // Too large
    ['5', '101'],
    ['2', '10'],
    ['0', '0'],
    ['5', '101'],
    ['100', '1100100'],
    [ExcelError::NAN(), '21'], // Invalid binary number
    [ExcelError::VALUE(), true], // Boolean okay for ODS, not for Excel/Gnumeric
    [ExcelError::VALUE(), false], // Boolean okay for ODS, not for Excel/Gnumeric
    ['-107', '1110010101'], // 2's Complement
    ['-1', '1111111111'], // 2's Complement
    ['-512', '1000000000'], // lowest negative
    ['511', '111111111'], // highest positive
    ['0', '0000000000'],
    ['1', '000000001'],
    ['256', '0100000000'],
    ['256', '100000000'],
    ['-256', '1100000000'],
];
