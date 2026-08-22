<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [51, '21-Dec-2000'],
    [52, '2000-01-01'],
    [1, '2000-01-03'],
    [52, '1995-01-01'],
    [1, '1995-01-07'],
    [2, '1995-01-10'],
    [1, '2018-01-01'],
    [ExcelError::VALUE(), '1800-01-01'],
    [53, false],
    [53, true],
    [53, 0],
    [53, 1],
    [53, 2],
    [1, 3],
    [2, 10],
    [53, '1904-01-01'],
    [ExcelError::VALUE(), '1900-01-01'],
    [ExcelError::VALUE(), '1903-12-31'],
    [ExcelError::VALUE(), '1900-01-07'],
    [ExcelError::NAN(), -1],
    [39, 1000],
    [25, '2001'],
    [25, 2002],
    [25, 2003],
    [25, 2004],
    [26, 2005],
];
