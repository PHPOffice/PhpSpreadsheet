<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [39493.0, '15-Jan-2008', 1],
    [39431.0, '15-Jan-2008', -1],
    [39522.0, '15-Jan-2008', 2],
    [39202.0, '31-Mar-2007', 1],
    [39141.0, '31-Mar-2007', -1],
    [39507.0, '31-Mar-2008', -1],
    [39416.0, '31-Mar-2008', -4],
    [39141.0, '29-Feb-2008', -12],
    [39248.0, '15-Mar-2007', 3],
    [22269.0, 22269.0, 0],
    [22269.0, 22269, 0],
    [22331.0, 22269.0, 2],
    [22331.0, 22269.75, 2],
    [25618.0, 22269.0, '110'],
    [18920.0, 22269.0, -110],
    [ExcelError::VALUE(), '15-Mar-2007', 'ABC'],
    [ExcelError::VALUE(), 'Invalid', 12],
    [ExcelError::VALUE(), true, 12],
    [ExcelError::VALUE(), '2020-01-01', false],
];
