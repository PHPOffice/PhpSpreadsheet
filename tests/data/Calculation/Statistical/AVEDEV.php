<?php

declare(strict_types=1);

return [
    [
        1.020408163265,
        [4, 5, 6, 7, 5, 4, 3],
    ],
    [
        1.65,
        [10.5, 7.2],
    ],
    [
        17.2,
        [7.2, 5.4, 45],
    ],
    [
        61.504,
        [10.5, 7.2, 200, 5.4, 8.1],
    ],
    [
        [1.825, '#VALUE!'],
        [
            // The index simulates a cell value
            // Numbers and Booleans are both counted
            '0.1.A' => 1,
            '0.2.A' => '2',
            '0.3.A' => 3.4,
            '0.4.A' => true,
            '0.5.A' => 5,
            '0.6.A' => null,
            '0.7.A' => 6.7,
            '0.8.A' => 'STRING',
            '0.9.A' => '',
        ],
    ],
    'numeric string' => [
        [1.825, 1.85],
        [1, '2', 3.4, true, 5, 6.7],
    ],
    'non-numeric string' => [
        // When non-numeric strings are passed directly, then a #VALUE! error is raised
        [1.825, '#VALUE!'],
        [1, '2', 3.4, true, 5, null, 6.7, 'STRING', ''],
    ],
    'no arguments' => [
        ['#NUM!', 'exception'],
        [],
    ],
];
