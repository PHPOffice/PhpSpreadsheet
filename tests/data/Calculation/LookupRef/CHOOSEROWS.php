<?php

declare(strict_types=1);

return [
    'select last row' => [
        [
            ['y', 'z', '#'],
        ],
        '=CHOOSEROWS(B3:D11, -1)',
    ],
    'fractional row' => [
        [
            ['y', 'z', '#'],
        ],
        '=CHOOSEROWS(B3:D11, -1.8)',
    ],
    'numeric string row' => [
        [
            ['y', 'z', '#'],
        ],
        '=CHOOSEROWS(B3:D11, " -1.8 ")',
    ],
    'select 1 row' => [
        [
            ['d', 'e', 'f'],
        ],
        '=CHOOSEROWS(B3:D11, 2)',
    ],
    'multiple rows including duplicates' => [
        [
            ['d', 'e', 'f'],
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
        ],
        '=CHOOSEROWS(B3:D11, 2, 1, 2)',
    ],
    'multiple rows mixed +/- mixed scalar/matrix' => [
        [
            ['d', 'e', 'f'],
            ['s', 't', 'u'],
            ['d', 'e', 'f'],
        ],
        '=CHOOSEROWS(B3:D11, 2, {-3; 2})',
    ],
    'reverse Rows' => [
        [
            ['y', 'z', '#'],
            ['v', 'w', 'x'],
            ['s', 't', 'u'],
            ['p', 'q', 'r'],
            ['m', 'n', 'o'],
            ['j', 'k', 'l'],
            ['g', 'h', 'i'],
            ['d', 'e', 'f'],
            ['a', 'b', 'c'],
        ],
        '=CHOOSEROWS(B3:D11, SEQUENCE(ROWS(B3:D11),,ROWS(B3:D11),-1))',
    ],
    'inline array' => [
        [
            ['g', 'h', 'i'],
            ['d', 'e', 'f'],
        ],
        '=CHOOSEROWS(B3:D11, {3;2})',
    ],
    'inline array with negative numbers' => [
        [
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=CHOOSEROWS(B3:D11, {-2;-1})',
    ],
    'named range' => [
        [
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=CHOOSEROWS(definedname, {-2;-1})',
    ],
    'only 1 argument' => [
        'exception',
        '=CHOOSEROWS(B3:D11)',
    ],
    'non-numeric row' => [
        '#VALUE!',
        '=CHOOSEROWS(B3:D11, "x")',
    ],
    'positive row too large' => [
        '#VALUE!',
        '=CHOOSEROWS(B3:D11, 10)',
    ],
    'negative row too large' => [
        '#VALUE!',
        '=CHOOSEROWS(B3:D11, 1, -10)',
    ],
    'zero row' => [
        '#VALUE!',
        '=CHOOSEROWS(B3:D11, 0)',
    ],
    'single cell' => [
        [
            ['a'],
        ],
        '=CHOOSEROWS(B3, 1)',
    ],
    'inline array rather than range' => [
        [
            [3, 4],
            [1, 2],
            [1, 2],
        ],
        '=CHOOSEROWS({1,2;3,4;5,6}, " 2.4 ", 1, 1)',
    ],
];
