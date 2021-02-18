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

$hiddenRows = [
    1 => false,
    2 => true,
    3 => false,
    4 => true,
    5 => false,
    6 => false,
    7 => false,
    8 => true,
    9 => false,
    10 => true,
    11 => true,
    12 => false,
];

return [
    [
        21,
        $hiddenRows,
        101,
        $baseTestData,
    ],
    [
        5,
        $hiddenRows,
        102,
        $baseTestData,
    ],
    [
        5,
        $hiddenRows,
        103,
        $baseTestData,
    ],
    [
        55,
        $hiddenRows,
        104,
        $baseTestData,
    ],
    [
        1,
        $hiddenRows,
        105,
        $baseTestData,
    ],
    [
        48620,
        $hiddenRows,
        106,
        $baseTestData,
    ],
    [
        23.1840462387393,
        $hiddenRows,
        107,
        $baseTestData,
    ],
    [
        20.7364413533277,
        $hiddenRows,
        108,
        $baseTestData,
    ],
    [
        105,
        $hiddenRows,
        109,
        $baseTestData,
    ],
    [
        537.5,
        $hiddenRows,
        110,
        $baseTestData,
    ],
    [
        430,
        $hiddenRows,
        111,
        $baseTestData,
    ],
];
