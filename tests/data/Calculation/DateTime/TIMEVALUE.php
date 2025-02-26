<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [0, '12:00:00 am'],
    [0.000717593, '12:01:02 am'],
    [0.502083333, '12:03 pm'],
    [0.504988426, '12:7:11 pm'],
    [0.176145833, '4:13:39'],
    [0.764085648, '6:20:17 pm'],
    [0.773229167, '18:33:27'],
    [0.143923611, '31/12/2007 03:27:15'],
    [0.906192133, '9:44:55 pm'],
    [ExcelError::VALUE(), 12],
    [0.5423611101, '13:01'],
    [0.40625, '33:45'],
    [ExcelError::VALUE(), '13:01PM'],
    [ExcelError::VALUE(), false],
    [ExcelError::VALUE(), true],
    'do not try to parse if no digits' => [ExcelError::VALUE(), 'x'],
];
