<?php

declare(strict_types=1);

return [
    [
        'expectedSlope' => [0.8, 0.813512072856517],
        'expectedIntersect' => [20.7, 20.671878197177865],
        'expectedGoodnessOfFit' => [0.904868, 0.9048681877346413],
        'expectedEquation' => 'Y = 20.67 * 0.81^X',
        'yValues' => [3, 10, 3, 6, 8, 12, 1, 4, 9, 14],
        'xValues' => [8, 2, 11, 6, 5, 4, 12, 9, 6, 1],
    ],
];
