<?php

declare(strict_types=1);

return [
    [
        'expectedSlope' => [-1.1, -1.1064189189190],
        'expectedIntersect' => [14.1, 14.081081081081],
        'expectedGoodnessOfFit' => [0.873138, 0.8731378215564962],
        'expectedEquation' => 'Y = 14.08 + -1.11 * X',
        'yValues' => [3, 10, 3, 6, 8, 12, 1, 4, 9, 14],
        'xValues' => [8, 2, 11, 6, 5, 4, 12, 9, 6, 1],
    ],
    [
        'expectedSlope' => [1.0, 1.0],
        'expectedIntersect' => [-2.0, -2.0],
        'expectedGoodnessOfFit' => [1.0, 1.0],
        'expectedEquation' => 'Y = -2 + 1 * X',
        'yValues' => [1, 2, 3, 4, 5],
        'xValues' => [3, 4, 5, 6, 7],
    ],
];
