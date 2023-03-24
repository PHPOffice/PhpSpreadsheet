<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [48, 0.2],
    [36, '0.4'],
    [24, 0.6],
    [12, 0.8],
    [15, '11-Nov-1918 11:15'],
    [59, '11:59 PM'],
    [59, '23:59:59'],
    [ExcelError::VALUE(), '31:62:93'],
    [0, 3600],
    [ExcelError::NAN(), -3600],
    [0, 12500],
    [0, 65535],
    [ExcelError::VALUE(), "Half past 1 O'Clock"],
    [0, false],
    [0, true],
];
