<?php

declare(strict_types=1);

return [
    [
        0.03263913041829,
        10.00001131, 9, 2, false,
    ],
    [
        0.06809400387,
        10.00001131, 9, 2, true,
    ],
    [
        0.112020903828,
        6, 3, 2, false,
    ],
    [
        0.576809918873,
        6, 3, 2, true,
    ],
    'Boolean as numeric' => [
        0.576809918873,
        6, 3, 2, 1,
    ],
    // Large x/b: the old fixed 32-term series diverged badly here.
    'Large x mid-range' => [
        0.522481190430,
        35, 35, 1, true,
    ],
    'Large x saturating' => [
        1.0,
        50, 2, 1, true,
    ],
    [
        1.0,
        80, 2, 1, true,
    ],
    [
        '#VALUE!',
        'NAN', 3, 2, true,
    ],
    [
        '#VALUE!',
        6, 'NAN', 2, true,
    ],
    [
        '#VALUE!',
        6, 3, 'NAN', true,
    ],
    [
        '#VALUE!',
        6, 3, 2, 'NAN',
    ],
    'Value < 0' => [
        '#NUM!',
        -6, 3, 2, true,
    ],
    'A < 0' => [
        '#NUM!',
        6, -3, 2, true,
    ],
    'A = 0' => [
        '#NUM!',
        6, 0, 2, true,
    ],
    'B < 0' => [
        '#NUM!',
        6, 3, -2, true,
    ],
    'B = 0' => [
        '#NUM!',
        6, 3, 0, true,
    ],
    // Large shape parameters; pdf references from mpmath (50-digit)
    'pdf alpha=143' => [
        0.02692582722231332,
        150, 143, 1, false,
    ],
    'pdf alpha=143 far tail' => [
        5.5379345193197405E-39,
        358, 143, 1, false,
    ],
    'pdf alpha=200' => [
        0.028197727685924278,
        200, 200, 1, false,
    ],
    'pdf alpha=150 beta=2' => [
        8.515496113839707E-15,
        150, 150, 2, false,
    ],
    'pdf alpha=1000' => [
        0.012614611348719664,
        1000, 1000, 1, false,
    ],
    'pdf alpha=100000' => [
        0.0012011991167748322,
        99900, 100000, 1, false,
    ],
    'cdf alpha=100000' => [
        0.5004205221103651,
        100000, 100000, 1, true,
    ],
    'pdf at 0, alpha below 1' => [
        '#NUM!',
        0, 0.5, 1, false,
    ],
    'pdf at 0, alpha 1' => [
        0.5,
        0, 1, 2, false,
    ],
    'pdf at 0, alpha above 1' => [
        0.0,
        0, 3, 1, false,
    ],
];
