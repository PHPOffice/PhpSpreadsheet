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
    // A criterion with <, <=, > or >= only matches values of the same type as its operand,
    // although text is greater than any number in a formula.
    'issue 3802 text is not greater than 0' => [
        6,
        [1, 2, 3, 5, 6, 23, null, 'Something'],
        '>0',
    ],
    'numeric criterion does not match numeric text or logical' => [
        1,
        [5, '6', true],
        '>0',
    ],
    'logical criterion does not match text' => [
        1,
        [false, 'FALSE', 'TA'],
        '<TRUE',
    ],
    'text criterion does not match number' => [
        1,
        [5, 'abc', 'zzz'],
        '<m',
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
    'text true criterion with mixed bool and string values' => [
        2,
        [true, false, false, 'true', false, true, false, false],
        'true',
    ],
    'text FALSE criterion' => [
        5,
        [true, false, false, true, false, true, false, false],
        'FALSE',
    ],
    'text FALSE criterion with mixed bool and string values' => [
        3,
        [true, '=false', '="FALSE"', true, false, true, false, 'FALSE'],
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
