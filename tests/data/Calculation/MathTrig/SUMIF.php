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
        '>"', // Compare to the single characater " (double quote)
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
        '>"', // Compare to the single characater " (double quote)
        [
            [10],
            [100],
        ],
    ],
];
