<?php

$baseMatrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
];

return [
    [
        $baseMatrix,
        2,
        2,
        [
            [1, 2],
            [4, 5],
        ],
    ],
    [
        $baseMatrix,
        1,
        2,
        [
            [1],
            [4],
        ],
    ],
    [
        $baseMatrix,
        2,
        1,
        [
            [1, 2],
        ],
    ],
    [
        $baseMatrix,
        4,
        4,
        [
            [1, 2, 3, null],
            [4, 5, 6, null],
            [7, 8, 9, null],
            [null, null, null, null],
        ],
    ],
    [
        $baseMatrix,
        2,
        4,
        [
            [1, 2],
            [4, 5],
            [7, 8],
            [null, null],
        ],
    ],
    [
        $baseMatrix,
        4,
        2,
        [
            [1, 2, 3, null],
            [4, 5, 6, null],
        ],
    ],
];
