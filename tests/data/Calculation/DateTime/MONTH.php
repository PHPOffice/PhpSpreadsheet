<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [1, 0],
    [12, 22269.0],
    [2, 30348.0],
    [6, 30843.0],
    [11, '11-Nov-1918'],
    [2, '28-Feb-1904'],
    [7, '01 Jul 2003'],
    [4, '38094'],
    [12, 'Dec 2003'],
    [12, '1960-12-25'],
    [11, '1918-11-01'],
    [ExcelError::VALUE(), '1918-13-11'],
    [ExcelError::NAN(), -10],
    [ExcelError::VALUE(), 'ABCD'],
    [1, false],
    [1, true],
    [3, 61],
    [2, 60], // because of fake leap day
    [12, 366], // because of fake leap day
    [1, 367],
];
