<?php

// nominal_rate, npery, Result

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

return [
    [
        0.053542667370758003,
        0.052499999999999998,
        4,
    ],
    [
        0.103812890625,
        0.10000000000000001,
        4,
    ],
    [
        0.10249999999999999,
        0.10000000000000001,
        2,
    ],
    [
        0.025156250000000002,
        0.025000000000000001,
        2,
    ],
    [
        Functions::NAN(),
        0.025,
        -1,
    ],
    [
        Functions::NAN(),
        -0.025,
        1,
    ],
    [
        Functions::VALUE(),
        0.025,
        'NaN',
    ],
    [
        Functions::VALUE(),
        'NaN',
        1,
    ],
];
