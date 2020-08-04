<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

// Settlement, Maturity, Frequency, Basis, Result

return [
    [
        110,
        '25-Jan-2007',
        '15-Nov-2008',
        2,
        1,
    ],
    [
        24,
        '2011-01-01',
        '2012-10-25',
        4,
    ],
    [
        ExcelException::VALUE(),
        'Invalid Date',
        '15-Nov-2008',
        2,
        1,
    ],
    [
        ExcelException::VALUE(),
        '25-Jan-2007',
        'Invalid Date',
        2,
        1,
    ],
    [
        ExcelException::NUM(),
        '25-Jan-2007',
        '15-Nov-2008',
        3,
        1,
    ],
];
