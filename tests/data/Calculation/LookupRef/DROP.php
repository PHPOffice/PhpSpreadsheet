<?php

declare(strict_types=1);

return [
    'drop last row' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
        ],
        '=DROP(B3:D11, -1)',
    ],
    'drop first 2 rows' => [
        [
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=DROP(B3:D11, 2)',
    ],
    'drop last 2 columns' => [
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
        '=DROP(B3:D11, , -2)',
    ],
    'drop first column' => [
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
        '=DROP(B3:D11, , 1)',
    ],
    'drop last row first column' => [
        [
            ['b', 'c'],
            ['e', 'f'],
            ['h', 'i'],
            ['k', 'l'],
            ['n', 'o'],
            ['q', 'r'],
            ['t', 'u'],
            ['w', 'x'],
        ],
        '=DROP(B3:D11, -1, 1)',
    ],
    'drop first 2 rows last 2 columns' => [
        [
            ['g'],
            ['j'],
            ['m'],
            ['p'],
            ['s'],
            ['v'],
            ['y'],
        ],
        '=DROP(B3:D11, 2, -2)',
    ],
    'drop first 2 rows last 2 columns fractional and string' => [
        [
            ['g'],
            ['j'],
            ['m'],
            ['p'],
            ['s'],
            ['v'],
            ['y'],
        ],
        '=DROP(B3:D11, 2.8, " -2.8 ")',
    ],
    'named range' => [
        [
            ['g'],
            ['j'],
            ['m'],
            ['p'],
            ['s'],
            ['v'],
            ['y'],
        ],
        '=DROP(definedname, 2,-2)',
    ],
    'only 1 argument' => [
        'exception',
        '=DROP(B3:D11)',
    ],
    'non-numeric row' => [
        '#VALUE!',
        '=DROP(B3:D11, "x")',
    ],
    'non-numeric column' => [
        '#VALUE!',
        '=DROP(B3:D11, 1, "x")',
    ],
    'positive row too large' => [
        '#VALUE!',
        '=DROP(B3:D11, 20)',
    ],
    'negative row too large' => [
        '#VALUE!',
        '=DROP(B3:D11, -20)',
    ],
    'positive column too large' => [
        '#VALUE!',
        '=DROP(B3:D11, , 20)',
    ],
    'negative column too large' => [
        '#VALUE!',
        '=DROP(B3:D11, , -20)',
    ],
    'row okay column too large' => [
        '#VALUE!',
        '=DROP(B3:D11, -1, 20)',
    ],
    'zero row' => [
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
        '=DROP(B3:D11, 0)',
    ],
    'zero column' => [
        [
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
            ['j', 'k', 'l'],
            ['m', 'n', 'o'],
            ['p', 'q', 'r'],
            ['s', 't', 'u'],
            ['v', 'w', 'x'],
            ['y', 'z', '#'],
        ],
        '=DROP(B3:D11, 1, 0)',
    ],
    'single cell' => [
        '#VALUE!',
        '=DROP(B3, 1)',
    ],
    'inline array rather than range' => [
        [
            [4],
            [6],
        ],
        '=DROP({1,2;3,4;5,6}, 1, 1)',
    ],
];
