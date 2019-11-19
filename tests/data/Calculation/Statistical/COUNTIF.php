<?php

return [
    [
        2,
        ['apples', 'oranges', 'peaches', 'apples'],
        'apples',
    ],
    [
        2,
        ['ApPlEs', 'oranges', 'peaches', 'APPles'],
        'aPpLeS',
    ],
    [
        2,
        [32, 54, 75, 86],
        '>55',
    ],
    [
        3,
        [32, 54, 75, 86],
        '<=75',
    ],
    [
        2,
        [6, 3, 4, 'X', ''],
        '<=4',
    ],
    [
        2,
        [6, 3, 4, 'X', ''],
        '<="4"',
    ],
];
