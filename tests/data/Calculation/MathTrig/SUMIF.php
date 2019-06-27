<?php

return [
    [
        15,
        [
            [1],
            [5],
            [10],
        ],
        '>=5',
    ],
    [
        10,
        [
            ['text'],
            [2],
        ],
        '=text',
        [
            [10],
            [100],
        ],
    ],
    [
        10,
        [
            ['"text with quotes"'],
            [2],
        ],
        '="text with quotes"',
        [
            [10],
            [100],
        ],
    ],
    [
        10,
        [
            ['"text with quotes"'],
            [''],
        ],
        '>"', // Compare to the single character " (double quote)
        [
            [10],
            [100],
        ],
    ],
    [
        100,
        [
            [''],
            ['anything'],
        ],
        '>"', // Compare to the single character " (double quote)
        [
            [10],
            [100],
        ],
    ],
    [
        10,
        [
            [1],
            [2],
        ],
        '<>', // any content
        [
            ['non-numeric value'], // ignored in SUM
            [10],
        ],
    ],
    [
        100,
        [
            ['0'],
            ['some text'],
        ],
        0, // Compare integer with string
        [
            [100],
            [1],
        ],
    ],
    [
        100,
        [
            [0],
            ['some text'],
        ],
        0, // Compare integer with integer
        [
            [100],
            [1],
        ],
    ],
    [
        3,
        [
            [1],
            [0],
            [1]
        ],
        1,
        [
            [3],
            [4] // less elements in sum array
        ]
    ],
    [
        3,
        [
            [1],
            [0] // less elements in condition array
        ],
        1,
        [
            [3],
            [4],
            [5]
        ]
    ]
];
