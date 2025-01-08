<?php

declare(strict_types=1);

return [
    'Add 2 rows' => [
        [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['#N/A', '#N/A', '#N/A'],
            ['#N/A', '#N/A', '#N/A'],
        ],
        '=EXPAND(B3:D4, 4)',
    ],
    'Add 1 colun' => [
        [
            ['a', 'b', 'c', '#N/A'],
            ['d', 'e', 'f', '#N/A'],
        ],
        '=EXPAND(B3:D4, , 4)',
    ],
    'Add 1 row 2 columns set cells to 0' => [
        [
            ['a', 'b', 'c', 0, 0],
            ['d', 'e', 'f', 0, 0],
            [0, 0, 0, 0, 0],
        ],
        '=EXPAND(B3:D4, 3, 5, 0)',
    ],
    'Fractional row and column' => [
        [
            ['a', 'b', 'c', 0, 0],
            ['d', 'e', 'f', 0, 0],
            [0, 0, 0, 0, 0],
        ],
        '=EXPAND(B3:D4, 3.2, 5.8, 0)',
    ],
    'only 1 argument' => [
        'exception',
        '=EXPAND(B3:D4)',
    ],
    'non-numeric row' => [
        '#VALUE!',
        '=EXPAND(B3:D4, "x")',
    ],
    'non-numeric column' => [
        '#VALUE!',
        '=EXPAND(B3:D4, 1, "x")',
    ],
    'row too small' => [
        '#VALUE!',
        '=EXPAND(B3:D4, 1)',
    ],
    'column too small' => [
        '#VALUE!',
        '=EXPAND(B3:D4, , 2)',
    ],
    'single cell' => [
        [
            ['a', 'xx'],
            ['xx', 'xx'],
        ],
        '=EXPAND(B3, 2, 2, "xx")',
    ],
    'inline array rather than range' => [
        [
            [1, 2, 0, 0],
            [3, 4, 0, 0],
            [5, 6, 0, 0],
            [0, 0, 0, 0],
        ],
        '=EXPAND({1,2;3,4;5,6}, 4.4, " 4.5 ", 0)',
    ],
];
