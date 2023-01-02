<?php

return [
    [
        64, '=SUM(4 * INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))',
    ],
    [
        64, '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) * 4)',
    ],
    [
        20, '=SUM(4 + INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))',
    ],
    [
        20, '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) + 4)',
    ],
    [
        -12, '=SUM(4 - INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))',
    ],
    [
        12, '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) - 4)',
    ],
    [
        0.25, '=SUM(4 / INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))',
    ],
    [
        4, '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) / 4)',
    ],
    'divide by zero' => [
        '#DIV/0!', '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) / 0)',
    ],
    'invalid divisor' => [
        '#VALUE!', '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) / "xyz")',
    ],
    [
        4294967296, '=SUM(4 ^ INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))',
    ],
    [
        65536, '=SUM(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)) ^ 4)',
    ],
];
