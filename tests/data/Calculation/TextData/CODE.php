<?php

declare(strict_types=1);

// Used to test both CODE and UNICODE.
// If expected result is array, 1st entry is for CODE, 2nd for UNICODE,
// and 3rd for CODE using MACROMAN.

return [
    [
        '#VALUE!',
        null,
    ],
    [
        '#VALUE!',
        '',
    ],
    [
        65,
        'ABC',
    ],
    [
        49,
        123,
    ],
    [
        84,
        true,
    ],
    [
        68,
        'DEF',
    ],
    [
        80,
        'PhpSpreadsheet',
    ],
    [
        49,
        1.5,
    ],
    [
        77,
        'Mark Baker',
    ],
    [
        109,
        'mark baker',
    ],
    [
        163,
        '£125.00',
    ],
    [
        [63, 12103],
        '⽇',
    ],
    [
        [156, 0x153, 207],
        'œ',
    ],
    [
        [131, 0x192, 196],
        'ƒ',
    ],
    [
        [63, 0x2105],
        '℅',
    ],
    [
        [63, 0x2211, 183],
        '∑',
    ],
    [
        [134, 0x2020, 160],
        '†',
    ],
    [
        [128, 8364, 219],
        '€',
    ],
    [
        [220, 220, 134],
        'Ü',
    ],
    'non-ascii but same win-1252 vs unicode' => [
        [0xD0, 0xD0, 63],
        'Ð',
    ],
    'ascii control character' => [
        2,
        "\x02",
    ],
    'omitted argument' => ['exception'],
];
