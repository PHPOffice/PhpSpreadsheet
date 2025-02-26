<?php

declare(strict_types=1);

return [
    'Cell Range' => [
        [
            [2, 4],
            [5, 9],
        ],
        'B4:E9',
    ],
    'Single Cell' => [
        [
            [2, 4],
            [2, 4],
        ],
        'B4',
    ],
    'Column Range' => [
        [
            [2, 1],
            [3, 1048576],
        ],
        'B:C',
    ],
    'Single Column Range' => [
        [
            [2, 1],
            [2, 1048576],
        ],
        'B:B',
    ],
    'Row Range' => [
        [
            [1, 2],
            [16384, 3],
        ],
        '2:3',
    ],
    'Single Row Range' => [
        [
            [1, 2],
            [16384, 2],
        ],
        '2:2',
    ],
];
