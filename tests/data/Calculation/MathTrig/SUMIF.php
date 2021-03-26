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
        'text',
        [
            [10],
            [100],
        ],
    ],
    [
        'incomplete', // 10,
        [
            ['"text with quotes"'],
            [2],
        ],
        '"text with quotes"',
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
            [1],
        ],
        1,
        [
            [3],
            [4], // less elements in sum array
        ],
    ],
    [
        3,
        [
            [1],
            [0], // less elements in condition array
        ],
        1,
        [
            [3],
            [4],
            [5],
        ],
    ],
    [
        157559,
        [['Jan'], ['Jan'], ['Jan'], ['Jan'], ['Feb'], ['Feb'], ['Feb'], ['Feb']],
        'Feb',
        [[36693], [22100], [53321], [34440], [29889], [50090], [32080], [45500]],
    ],
    [
        66582,
        [['North 1'], ['North 2'], ['South 1'], ['South 2'], ['North 1'], ['North 2'], ['South 1'], ['South 2']],
        'North 1',
        [[36693], [22100], [53321], [34440], [29889], [50090], [32080], [45500]],
    ],
    [
        138772,
        [['North 1'], ['North 2'], ['South 1'], ['South 2'], ['North 1'], ['North 2'], ['South 1'], ['South 2']],
        'North ?',
        [[36693], [22100], [53321], [34440], [29889], [50090], [32080], [45500]],
    ],
    [
        '#DIV/0!',
        [['North 1'], ['North 2'], ['South 1'], ['South 2'], ['North 1'], ['North 2'], ['South 1'], ['South 2']],
        'North ?',
        [['=3/0'], [22100], [53321], [34440], [29889], [50090], [32080], [45500]],
    ],
    [
        138772,
        [['North 1'], ['North 2'], ['South 1'], ['South 2'], ['North 1'], ['North 2'], ['South 1'], ['South 2']],
        'North ?',
        [[36693], [22100], ['=3/0'], [34440], [29889], [50090], [32080], [45500]],
    ],
];
