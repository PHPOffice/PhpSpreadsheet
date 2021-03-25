<?php

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

return [
    [
        0.09141696292534264,
        '31-Mar-2008', '1-Jun-2008', 98.45,
    ],
    [
        Functions::VALUE(),
        'Not a Valid Date', '1-Jun-2008', 98.45,
    ],
    [
        Functions::VALUE(),
        '31-Mar-2008', 'Not a Valid Date', 98.45,
    ],
    [
        Functions::VALUE(),
        '31-Mar-2008', '1-Jun-2008', 'NaN',
    ],
    [
        Functions::NAN(),
        '31-Mar-2008', '1-Jun-2008', -1.25,
    ],
    [
        Functions::NAN(),
        '31-Mar-2000', '1-Jun-2021', 97.25,
    ],
    [
        Functions::NAN(),
        '1-Jun-2008', '31-Mar-2008', 97.25,
    ],
    [
        0.024405125076266018,
        '1-Feb-2017', '30-Jun-2017', 99,
    ],
];
