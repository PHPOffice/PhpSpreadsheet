<?php

$baseTestData = [
    1 => ['A' => 123],
    2 => ['A' => 234],
    3 => ['A' => '=SUBTOTAL(1, A1:A2)'],
    4 => ['A' => '=ROMAN(SUBTOTAL(1, A1:A2))'],
    5 => ['A' => 'This is text containing "=" and "SUBTOTAL("'],
];

return [
    [
        357,
        9,
        $baseTestData,
    ],
];
