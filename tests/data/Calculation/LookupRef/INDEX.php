<?php

declare(strict_types=1);

return [
    [
        [20 => ['R' => 1]], // Expected
        // Input
        [20 => ['R' => 1]],
    ],
    'Negative Row' => [
        '#VALUE!', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        -1,
    ],
    'Row > matrix rows' => [
        '#REF!', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        10,
    ],
    'Row is not a number' => [
        '#VALUE!', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        'NaN',
    ],
    'Row is Error' => [
        '#N/A', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        '#N/A',
    ],
    'Return row 2' => [
        [21 => ['R' => 2]], // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        2,
    ],
    'Return row 2 from larger matrix' => [
        [21 => ['R' => 2, 'S' => 4]], // Expected
        // Input
        [
            '20' => ['R' => 1, 'S' => 3],
            '21' => ['R' => 2, 'S' => 4],
        ],
        2,
        0,
    ],
    'Negative Column' => [
        '#VALUE!', // Expected
        // Input
        [
            '20' => ['R' => 1, 'S' => 3],
            '21' => ['R' => 2, 'S' => 4],
        ],
        0,
        -1,
    ],
    'Column > matrix columns' => [
        '#REF!', // Expected
        // Input
        [
            '20' => ['R' => 1, 'S' => 3],
            '21' => ['R' => 2, 'S' => 4],
        ],
        2,
        10,
    ],
    'Column is not a number' => [
        '#VALUE!', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        1,
        'NaN',
    ],
    'Column is Error' => [
        '#N/A', // Expected
        // Input
        [
            20 => ['R' => 1],
            21 => ['R' => 2],
        ],
        1,
        '#N/A',
    ],
    [
        4, // Expected
        // Input
        [
            '20' => ['R' => 1, 'S' => 3],
            '21' => ['R' => 2, 'S' => 4],
        ],
        2,
        2,
    ],
    [
        [4], // Expected
        // Input
        [
            '20' => ['R' => 1, 'S' => 3],
            '21' => ['R' => 2, 'S' => 4],
        ],
        [
            '21' => ['R' => 2],
        ],
        [
            '21' => ['R' => 2],
        ],
    ],
    [
        'Pears',
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        2,
    ],
    [
        'Bananas',
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        1,
    ],
    [
        [1 => ['Bananas', 'Pears']],
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        0,
    ],
    [
        3,
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        5,
        2,
    ],
    [
        [4 => [8, 3]],
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        5,
        0,
    ],
    [
        [
            [6],
            [3],
            [9],
            [5],
            [3],
        ],
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        0,
        2,
    ],
];
