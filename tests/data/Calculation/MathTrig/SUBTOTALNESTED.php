<?php

$baseTestData = [
    1 => ['A' => 1],
    2 => ['A' => 1],
    3 => ['A' => '=SUBTOTAL(1, A1:A2)'],
    4 => ['A' => '=ROMAN(SUBTOTAL(1, A1:A2))']
];

return [
    [
        2,
        2,
        $baseTestData,
    ],
];
