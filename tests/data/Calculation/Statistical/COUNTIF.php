<?php

declare(strict_types=1);

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
        [6, 3, 4, 'X', '', null],
        '<=4',
    ],
    [
        2,
        [6, 3, 4, 31, 'X', '', null],
        '<="4"',
    ],
    [
        2,
        [0, 1, 1, 2, 3, 5, 8, 0, 13, 21],
        0,
    ],
    [
        3,
        [true, false, false, true, false, true, false, false],
        true,
    ],
    [
        5,
        [true, false, false, true, false, true, false, false],
        '<>true',
    ],
    // Excel reads a criterion string as the value it spells, as it reads "32" as the number 32,
    // so "TRUE" and "FALSE" match logical values just as TRUE and FALSE do.
    'text TRUE criterion' => [
        3,
        [true, false, false, true, false, true, false, false],
        'TRUE',
    ],
    'text true criterion' => [
        3,
        [true, false, false, true, false, true, false, false],
        'true',
    ],
    'text FALSE criterion' => [
        5,
        [true, false, false, true, false, true, false, false],
        'FALSE',
    ],
    [
        4,
        ['apples', 'oranges', 'peaches', 'apples'],
        '*',
    ],
    [
        3,
        ['apples', 'oranges', 'peaches', 'apples'],
        '*p*s*',
    ],
    [
        2,
        ['apples', 'oranges', 'peaches', 'apples'],
        '?????es',
    ],
    [
        2,
        ['great * ratings', 'bad * ratings', 'films * wars', 'films * trek', 'music * radio'],
        '*~* ra*s',
    ],
];
