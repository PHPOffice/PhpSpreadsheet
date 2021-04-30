<?php

// effect_rate, npery, result

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

return [
    [
        0.052500319868356002,
        0.053543,
        4,
    ],
    [
        0.096454756337780001,
        0.10000000000000001,
        4,
    ],
    [
        0.097617696340302998,
        0.10000000000000001,
        2,
    ],
    [
        0.024718035238113001,
        0.025000000000000001,
        12,
    ],
    [
        Functions::NAN(),
        -0.025,
        12,
    ],
    [
        Functions::NAN(),
        0.025,
        -12,
    ],
    [
        Functions::VALUE(),
        'NaN',
        12,
    ],
    [
        Functions::VALUE(),
        0.025,
        'NaN',
    ],
];
