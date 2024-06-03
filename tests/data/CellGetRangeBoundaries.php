<?php

declare(strict_types=1);

return [
    'Cell Range' => [
        [
            ['B', 4],
            ['E', 9],
        ],
        'B4:E9',
    ],
    'Single Cell' => [
        [
            ['B', 4],
            ['B', 4],
        ],
        'B4',
    ],
    'Column Range' => [
        [
            ['B', 1],
            ['C', 1048576],
        ],
        'B:C',
    ],
    'Single Column Range' => [
        [
            ['B', 1],
            ['B', 1048576],
        ],
        'B:B',
    ],
    'Row Range' => [
        [
            ['A', 2],
            ['XFD', 3],
        ],
        '2:3',
    ],
    'Single Row Range' => [
        [
            ['A', 2],
            ['XFD', 2],
        ],
        '2:2',
    ],
];
