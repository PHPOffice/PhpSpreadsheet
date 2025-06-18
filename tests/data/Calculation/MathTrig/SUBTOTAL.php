<?php

$baseTestData = [
    1 => ['A' => 0],
    2 => ['A' => 1],
    3 => ['A' => 1],
    4 => ['A' => 2],
    5 => ['A' => 3],
    6 => ['A' => 5],
    7 => ['A' => 8],
    8 => ['A' => 13],
    9 => ['A' => 21],
    10 => ['A' => 34],
    11 => ['A' => 55],
    12 => ['A' => 89],
];

return [
    [
        19.3333333333333,
        1,
        $baseTestData,
    ],
    [
        12,
        2,
        $baseTestData,
    ],
    [
        12,
        3,
        $baseTestData,
    ],
    [
        89,
        4,
        $baseTestData,
    ],
    [
        0,
        5,
        $baseTestData,
    ],
    [
        0,
        6,
        $baseTestData,
    ],
    [
        27.5196899207337,
        7,
        $baseTestData,
    ],
    [
        26.3480971271593,
        8,
        $baseTestData,
    ],
    [
        232,
        9,
        $baseTestData,
    ],
    [
        757.3333333333330,
        10,
        $baseTestData,
    ],
    [
        694.2222222222220,
        11,
        $baseTestData,
    ],
];
