<?php

function orderGrid(): array
{
    return [
        ['Order ID', 10247, 10249, 10250, 10251, 10252, 10253],
        ['Unit Price', 14.00, 18.60, 7.70, 16.80, 16.80, 64.80],
        ['Quantity', 12, 9, 10, 6, 20, 40],
    ];
}

function partsGrid(): array
{
    return [
        ['Axles', 'Bearings', 'Bolts'],
        [4, 4, 9],
        [5, 7, 10],
        [6, 8, 11],
    ];
}

return [
    [
        16.80,
        10251,
        orderGrid(),
        2,
        false,
    ],
    [
        6.0,
        10251,
        orderGrid(),
        3,
        false,
    ],
    [
        '#N/A',
        10248,
        orderGrid(),
        2,
        false,
    ],
    [
        14.0,
        10248,
        orderGrid(),
        2,
        true,
    ],
    [
        4,
        'Axles',
        partsGrid(),
        2,
        true,
    ],
    [
        7,
        'Bearings',
        partsGrid(),
        3,
        false,
    ],
    [
        5,
        'B',
        partsGrid(),
        3,
        true,
    ],
    [
        5,
        'B',
        partsGrid(),
        3,
        null,
    ],
    [
        11,
        'Bolts',
        partsGrid(),
        4,
    ],
    [
        'c',
        3,
        [
            [1, 2, 3],
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
        ],
        2,
        true,
    ],
    [
        3,
        3,
        [
            [1, 2, 3],
        ],
        1,
        true,
    ],
    [
        5,
        'x',
        [
            ['Selection column', '0', '0', '0', '0', 'x', 'x', 'x', 'x', 'x'],
            ['Value to retrieve', 1, 2, 3, 4, 5, 6, 7, 8, 9],
        ],
        2,
        false,
    ],
    [
        2,
        'B',
        [
            ['Selection column', 'C', 'B', 'A'],
            ['Value to retrieve', 3, 2, 1],
        ],
        2,
        false,
    ],
    [
        '#VALUE!',
        'B',
        [
            ['Selection column', 'C', 'B', 'A'],
            ['Value to retrieve', 3, 2, 1],
        ],
        'Nan',
        false,
    ],
    [
        '#N/A',
        'B',
        [
            'Selection column',
            'Value to retrieve',
        ],
        2,
        false,
    ],
    [
        '#REF!',
        'Selection column',
        [
            'Selection column',
            'Value to retrieve',
        ],
        5,
        false,
    ],
    [
        'Selection column',
        'Selection column',
        [
            'Selection column',
            'Value to retrieve',
        ],
        1,
        false,
    ],
    [
        0.61,
        'Ed',
        [
            [null, 'Ann', 'Cara', 'Colin', 'Ed', 'Frank'],
            ['Math', 0.58, 0.90, 0.67, 0.76, 0.80],
            ['French', 0.61, 0.71, 0.59, 0.59, 0.76],
            ['Physics', 0.75, 0.45, 0.39, 0.52, 0.69],
            ['Bioogy', 0.39, 0.55, 0.77, 0.61, 0.45],
        ],
        5,
        false,
    ],
    [
        'Normal Weight',
        23.5,
        [
            [null, 'Min', 0.0, 18.5, 25.0, 30.0],
            ['BMI', 'Max', 18.4, 24.9, 29.9, null],
            [null, 'Body Type', 'Underweight', 'Normal Weight', 'Overweight', 'Obese'],
        ],
        3,
        true,
    ],
    'issue2934' => [
        'Red',
        102,
        [
            [null, 102],
            [null, 'Red'],
        ],
        2,
        false,
    ],
];
