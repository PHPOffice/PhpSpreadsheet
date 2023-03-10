<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [1900, 0],
    [1900, 1],
    [1991, 33333.33],
    [1960, '22269.0'],
    [1983, 30348.0],
    [1984, 30843.0],
    [2525, '01 Jan 2525'],
    [1918, '11-Nov-1918'],
    [1904, '28-Feb-1904'],
    [ExcelError::NAN(), -10],
    [ExcelError::VALUE(), '31-Dec-1899'],
    [ExcelError::VALUE(), 'ABCD'],
    [1900, false],
    [1900, true],
    [1900, 366], // because of fake leap day
    [1901, 367],
];
