<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

//  OpenOffice Result, Argument
return [
    [19, 22269],
    [1, 30348],
    [10, 30843],
    [11, '11-Nov-1918'],
    [28, '28-Feb-1904'],
    [ExcelError::VALUE(), '30-Feb-1904'],
    [ExcelError::VALUE(), 'Invalid'],
    // The following will all differ between Excel and OpenOffice
    [29, -1],
    [31, 1],
    [30, 0.5],
    [30, 0],
    [31, true],
    [30, false],
];
