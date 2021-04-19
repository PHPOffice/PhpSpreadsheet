<?php

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

return [
    [
        98.45,
        '31-Mar-2008', '1-Jun-2008', 0.09,
    ],
    [
        Functions::VALUE(),
        'Not a Valid Date', '1-Jun-2008', 0.09,
    ],
    [
        Functions::VALUE(),
        '31-Mar-2008', 'Not a Valid Date', 0.09,
    ],
    [
        Functions::VALUE(),
        '31-Mar-2008', '1-Jun-2008', 'NaN',
    ],
    [
        Functions::NAN(),
        '31-Mar-2008', '1-Jun-2008', -0.09,
    ],
    [
        Functions::NAN(),
        '31-Mar-2000', '1-Jun-2021', 0.09,
    ],
    [
        Functions::NAN(),
        '1-Jun-2008', '31-Mar-2008', 0.09,
    ],
    [
        0.0,
        '31-Mar-2008', '1-Apr-2008', 360,
    ],
    [
        Functions::NAN(),
        '31-Mar-2008', '1-Apr-2008', 361,
    ],
    [
        97.75,
        '1-Apr-2017', '30-Jun-2017', 0.09,
    ],
    [
        97.543194444,
        '5-Feb-2019', '1-Feb-2020', 0.0245,
    ],
    [
        98.86180556,
        '1-Feb-2017', '30-Jun-2017', 0.0275,
    ],
];
