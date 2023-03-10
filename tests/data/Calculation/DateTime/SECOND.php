<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [57, 0.2339930556],
    [13, 0.4202893519],
    [22, 0.60789351845],
    [11, 0.8022106481],
    [35, '11-Nov-1918 11:15:35'],
    [0, '11:59 PM'],
    [59, '23:59:59'],
    [0, 3600],
    [ExcelError::VALUE(), '23:59:99'],
    [ExcelError::NAN(), -3601],
    [0, 12500],
    [0, 65535],
    [ExcelError::VALUE(), "Half past 1 O'Clock"],
    [0, false],
    [0, true],
];
