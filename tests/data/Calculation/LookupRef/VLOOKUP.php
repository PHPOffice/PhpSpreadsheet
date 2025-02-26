<?php

declare(strict_types=1);

function densityGrid(): array
{
    return [
        ['Density', 'Viscosity', 'Temperature'],
        [0.457, 3.55, 500],
        [0.525, 3.25, 400],
        [0.616, 2.93, 300],
        [0.675, 2.75, 250],
        [0.746, 2.57, 200],
        [0.835, 2.38, 150],
        [0.946, 2.17, 100],
        [1.090, 1.95, 50],
        [1.290, 1.71, 0],
    ];
}

return [
    [
        '#N/A',
        1,
        densityGrid(),
        2,
        false,
    ],
    [
        '#REF!',
        1,
        'HELLO WORLD',
        2,
        false,
    ],
    [
        100,
        1,
        densityGrid(),
        3,
        true,
    ],
    [
        '#N/A',
        0.70,
        densityGrid(),
        3,
        false,
    ],
    [
        '#N/A',
        0.100,
        densityGrid(),
        2,
        true,
    ],
    [
        1.71,
        2,
        densityGrid(),
        2,
        true,
    ],
    [
        5,
        'x',
        [
            [
                'Selection column',
                'Value to retrieve',
            ],
            ['0', 1],
            ['0', 2],
            ['0', 3],
            ['0', 4],
            ['x', 5],
            ['x', 6],
            ['x', 7],
            ['x', 8],
            ['x', 9],
        ],
        2,
        false,
    ],
    [
        '#N/A',
        '10y2',
        [
            ['5y-1', 2.0],
            ['10y1', 7.0],
            ['10y2', 10.0],
        ],
        2.0,
    ],
    [
        '#VALUE!',
        '10y2',
        [
            ['5y-1', 2.0],
            ['10y1', 7.0],
            ['10y2', 10.0],
        ],
        -5,
    ],
    [
        '#REF!',
        '10y2',
        [
        ],
        2.0,
    ],
    [
        '#REF!',
        '10y2',
        [
            [2.0],
            [7.0],
            [10.0],
        ],
        2.0,
    ],
    [
        3.50,
        'Cornflakes',
        [
            ['Item Description', 'Price'],
            ['Tinned Tomatoes', 0.90],
            ['Tinned Tuna', 1.50],
            ['Cornflakes', 3.50],
            ['Shortcake Biscuits', 1.00],
            ['Toothpaste', 4.10],
            ['Tinned Baked Beans', 0.99],
            ['White Sliced Bread', 0.80],
        ],
        2,
        false,
    ],
    [
        'E',
        0.52,
        [
            ['Lower', 'Upper', 'Grade'],
            [0.00, 0.44, 'F'],
            [0.45, 0.54, 'E'],
            [0.55, 0.64, 'D'],
            [0.65, 0.74, 'C'],
            [0.75, 0.84, 'B'],
            [0.85, 1.00, 'A'],
        ],
        3,
        true,
    ],
    [
        'E',
        0.52,
        [
            ['Lower', 'Upper', 'Grade'],
            [0.00, 0.44, 'F'],
            [0.45, 0.54, 'E'],
            [0.55, 0.64, 'D'],
            [0.65, 0.74, 'C'],
            [0.75, 0.84, 'B'],
            [0.85, 1.00, 'A'],
        ],
        3,
        null,
    ],
    'issue2934' => [
        'Red',
        102,
        [
            [null, null],
            [102, 'Red'],
        ],
        2,
        false,
    ],
    'string supplied as index' => [
        '#VALUE!',
        102,
        [
            [null, null],
            [102, 'Red'],
        ],
        'xyz',
        false,
    ],
    'num error propagated' => [
        '#NUM!',
        102,
        [
            [null, null],
            [102, 'Red'],
        ],
        '=SQRT(-1)',
        false,
    ],
    'issue 3561' => [
        7,
        6,
        [
            [1, 2, 3, 4, 5],
            [6, 7, 8, 9, 10],
            [11, 12, 13, 14, 15],
        ],
        [[2], [3], [2]],
    ],
];
