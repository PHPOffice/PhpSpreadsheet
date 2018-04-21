<?php

$baseTestData = [
    1 => ['A' => 1],
    2 => ['A' => '=2*1'],
    3 => ['A' => '=SUBTOTAL(1, A1:A2)'],
    4 => ['A' => '=ROMAN(SUBTOTAL(1, A1:A2))'],
    5 => ['A' => 'This is text containing "=" and "SUBTOTAL("'],
];

return [
    [
        2,
        2,
        $baseTestData,
    ],
];
