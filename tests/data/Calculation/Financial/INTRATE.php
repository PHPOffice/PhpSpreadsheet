<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

// Settlement, Maturity, Investment, Redemption, Basis, Result

return [
    [
        0.057680000000000002,
        '2008-02-15',
        '2008-05-15',
        1000000,
        1014420,
        2,
    ],
    [
        0.22500000000000001,
        '2005-04-01',
        '2010-03-31',
        1000,
        2125,
    ],
    [
        ExcelException::VALUE(),
        '2008-02-15',
        '2008-05-15',
        1000000,
        1014420,
        'ABC',
    ],
    [
        ExcelException::NUM(),
        '2008-02-15',
        '2008-05-15',
        1000000,
        -1014420,
        2,
    ],
    [
        ExcelException::VALUE(),
        'Invalid Date',
        '2008-05-15',
        1000000,
        1014420,
        2,
    ],
];
