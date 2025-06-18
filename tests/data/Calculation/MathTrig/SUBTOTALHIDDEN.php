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
        21,
        101,
        $baseTestData,
    ],
    [
        5,
        102,
        $baseTestData,
    ],
    [
        5,
        103,
        $baseTestData,
    ],
    [
        55,
        104,
        $baseTestData,
    ],
    [
        1,
        105,
        $baseTestData,
    ],
    [
        48620,
        106,
        $baseTestData,
    ],
    [
        23.1840462387393,
        107,
        $baseTestData,
    ],
    [
        20.7364413533277,
        108,
        $baseTestData,
    ],
    [
        105,
        109,
        $baseTestData,
    ],
    [
        537.5,
        110,
        $baseTestData,
    ],
    [
        430,
        111,
        $baseTestData,
    ],
];
