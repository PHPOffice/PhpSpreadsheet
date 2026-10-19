<?php

declare(strict_types=1);

return [
    [
        0.520499877813,
        0.5, 1, true,
    ],
    [
        0.207553748710,
        2, 3, false,
    ],
    [
        0.111565080074,
        3, 2, false,
    ],
    [
        0.776869839852,
        3, 2, true,
    ],
    [
        0.039646370521,
        3, 9, false,
    ],
    [
        0.035705027315,
        3, 9, true,
    ],
    [
        0.103349469094,
        7.5, 8, false,
    ],
    [
        0.516232618446,
        7.5, 8, true,
    ],
    [
        0.020666985354,
        8, 3, false,
    ],
    [
        0.953988294311,
        8, 3, true,
    ],
    [
        '#VALUE!',
        'NaN', 3, true,
    ],
    [
        '#VALUE!',
        2, 'NaN', true,
    ],
    [
        '#VALUE!',
        2, 3, 'NaN',
    ],
    'Value < 0' => [
        '#NUM!',
        -8, 3, true,
    ],
    'Degrees < 1' => [
        '#NUM!',
        8, 0, true,
    ],
    // Large degrees of freedom; pdf references from mpmath (50-digit)
    'pdf df=344' => [
        0.015147109667695774,
        345, 344, false,
    ],
    'pdf df=500' => [
        0.012611458092262507,
        500, 500, false,
    ],
    'pdf df=500 low tail' => [
        0.00366869999431594,
        450, 500, false,
    ],
    'pdf df=2000' => [
        0.00630730567436233,
        2000, 2000, false,
    ],
    'cdf df=200000' => [
        0.5004205221103651,
        200000, 200000, true,
    ],
    'pdf at 0, df=1' => [
        '#NUM!',
        0, 1, false,
    ],
    'pdf at 0, df=2' => [
        0.5,
        0, 2, false,
    ],
    'pdf at 0, df=3' => [
        0.0,
        0, 3, false,
    ],
];
