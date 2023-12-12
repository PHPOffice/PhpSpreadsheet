<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [6, 0.25],
    [18, 0.75],
    [12, 0.5],
    [14, 0.6],
    [11, '11-Nov-1918 11:11'],
    [23, '11:59 PM'],
    [23, '23:59:59'],
    [0, 3600],
    [ExcelError::VALUE(), '31:62:93'],
    [ExcelError::NAN(), -3600],
    [0, 7200],
    [0, 65535],
    [ExcelError::VALUE(), "1 O'Clock"],
    [0, false],
    [0, true],
];
