<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [39507.0, '15-Jan-2008', 1],
    [39447.0, '15-Jan-2008', -1],
    [39538.0, '15-Jan-2008', 2],
    [39202.0, '31-Mar-2007', 1],
    [39141.0, '31-Mar-2007', -1],
    [39507.0, '31-Mar-2008', -1],
    [39416.0, '31-Mar-2008', -4],
    [39141.0, '29-Feb-2008', -12],
    [39263.0, '15-Mar-2007', 3],
    [22281.0, 22269.0, 0],
    [22340.0, '22269.0', 2],
    [25627.0, 22269.0, 110],
    [18932.0, 22269.0, -110],
    [22371.0, 22269.0, 3],
    [22371.0, 22269.75, 3],
    [22371.0, 22269.0, 3.75],
    [ExcelError::VALUE(), '15-Mar-2007', false],
    [ExcelError::VALUE(), '15-Mar-2007', true],
    [ExcelError::VALUE(), '15-Mar-2007', 'ABC'],
    [ExcelError::VALUE(), 'Invalid', 12],
    [ExcelError::VALUE(), true, 12],
    [ExcelError::VALUE(), '2020-01-01', false],
];
