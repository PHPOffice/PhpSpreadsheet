<?php

declare(strict_types=1);

return [
    'take last row' => [
        [
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, -1)',
    ],
    'take first 2 rows' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
        ],
        '=TAKE(B3:D11, 2)',
    ],
    'take last 2 columns' => [
        [
            ['b', 'c'],
            ['e', 'f'],
            ['h', 'i'],
            ['k', 'l'],
            ['n', 'o'],
            ['q', 'r'],
            ['t', 'u'],
            ['w', 'x'],
            ['z', '#'],
        ],
        '=TAKE(B3:D11, , -2)',
    ],
    'take first column' => [
        [
            ['a'],
            ['d'],
            ['g'],
            ['j'],
            ['m'],
            ['p'],
            ['s'],
            ['v'],
            ['y'],
        ],
        '=TAKE(B3:D11, , 1)',
    ],
    'take last row first column' => [
        [
            ['y'],
        ],
        '=TAKE(B3:D11, -1, 1)',
    ],
    'take first 2 rows last 2 columns' => [
        [
            ['b', 'c'],
            ['e', 'f'],
        ],
        '=TAKE(B3:D11, 2, -2)',
    ],
    'take first 2 rows last 2 columns fractional and string' => [
        [
            ['b', 'c'],
            ['e', 'f'],
        ],
        '=TAKE(B3:D11, 2.8, " -2.8 ")',
    ],
    'named range' => [
        [
            ['x'],
            ['#'],
        ],
        '=TAKE(definedname, -2,-1)',
    ],
    'only 1 argument' => [
        'exception',
        '=TAKE(B3:D11)',
    ],
    'non-numeric row' => [
        '#VALUE!',
        '=TAKE(B3:D11, "x")',
    ],
    'non-numeric column' => [
        '#VALUE!',
        '=TAKE(B3:D11, 1, "x")',
    ],
    'positive row too large' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, 20)',
    ],
    'negative row too large' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, -20)',
    ],
    'positive column too large' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, , 20)',
    ],
    'negative column too large' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, , -20)',
    ],
    'row okay column too large' => [
        [
            ['y', 'z', '#'],
        ],
        '=TAKE(B3:D11, -1, 20)',
    ],
    'zero row' => [
        '#VALUE!',
        '=TAKE(B3:D11, 0)',
    ],
    'zero column' => [
        '#VALUE!',
        '=TAKE(B3:D11, 1, 0)',
    ],
    'single cell' => [
        [
            ['a'],
        ],
        '=TAKE(B3, 1)',
    ],
    'inline array rather than range' => [
        [
            [1, 2],
        ],
        '=TAKE({1,2;3,4;5,6}, 1, 2)',
    ],
];
