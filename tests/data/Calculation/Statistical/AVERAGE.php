<?php

declare(strict_types=1);

return [
    [
        11,
        [10, 7, 9, 27, 2],
    ],
    [
        10,
        [10, 7, 9, 27, 2, 5],
    ],
    [
        19,
        [10, 15, 32],
    ],
    [
        8.85,
        [10.5, 7.2],
    ],
    [
        19.2,
        [7.2, 5.4, 45],
    ],
    [
        46.24,
        [10.5, 7.2, 200, 5.4, 8.1],
    ],
    [
        [4.025, '#VALUE!'],
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
    [
        [4.025, 3.183333333333],
        [1, '2', 3.4, true, 5, 6.7],
    ],
    [
        // When non-numeric strings are passed directly, then a #VALUE! error is raised
        [4.025, '#VALUE!'],
        [1, '2', 3.4, true, 5, null, 6.7, 'STRING', ''],
    ],
    'no arguments' => [
        ['#DIV/0!', 'exception'],
        [],
    ],
];
