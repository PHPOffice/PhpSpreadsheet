<?php

declare(strict_types=1);

return [
    [
        2.453736570842,
        4.5,
    ],
    [
        1.791759469228,
        4,
    ],
    [
        '#VALUE!',
        'NAN',
    ],
    'Value < 0' => [
        '#NUM!',
        -4.5,
    ],
    'Value = 0' => [
        '#NUM!',
        0.0,
    ],
    // Values beyond Gamma overflow (x > ~171.62); references from mpmath loggamma
    'Value = 172' => [
        711.714725802290,
        172,
    ],
    'Value = 200' => [
        857.933669825857,
        200,
    ],
    'Value = 1000' => [
        5905.220423209181,
        1000,
    ],
    'Value = 100000' => [
        1051287.7089736569,
        100000,
    ],
    'lnGamma itself overflows a double' => [
        '#NUM!',
        1.0E+306,
    ],
];
