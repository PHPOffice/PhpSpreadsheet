<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

//  Excel Result, Argument
return [
    [19, 22269],
    [1, 30348],
    [10, 30843],
    [11, '11-Nov-1918'],
    [28, '28-Feb-1904'],
    [25, '1960-12-25'],
    [ExcelError::VALUE(), '30-Feb-1904'],
    [ExcelError::VALUE(), 'Invalid'],
    // The following will all differ between Excel and OpenOffice
    [ExcelError::NAN(), -1],
    [1, 1],
    [0, 0.5],
    [0, 0],
    [1, true],
    [0, false],
];
