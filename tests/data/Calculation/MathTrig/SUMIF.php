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
];
