<?php

declare(strict_types=1);

return [
    [
        '#VALUE!',
        'ABC',
    ],
    [
        '#VALUE!',
        -5,
    ],
    [
        '#VALUE!',
        0,
    ],
    [
        'A',
        65,
    ],
    [
        '{',
        123,
    ],
    [
        '~',
        126,
    ],
    [
        'Á',
        193,
    ],
    [
        'ÿ',
        255,
    ],
    [
        '#VALUE!',
        256,
    ],
    [
        '#VALUE!', // '⽇',
        12103,
    ],
    [
        '#VALUE!', // 'œ',
        0x153,
    ],
    [
        '#VALUE!', // 'ƒ',
        0x192,
    ],
    [
        '#VALUE!', // '℅',
        0x2105,
    ],
    [
        '#VALUE!', // '∑',
        0x2211,
    ],
    [
        '#VALUE!', // '†',
        0x2020,
    ],
    'omitted argument' => ['exception'],
    'non-printable' => ["\x02", 2],
    'bool argument' => ["\x01", true],
    'null argument' => ['#VALUE!', null],
    'ascii 1 is 49' => ['1', 49],
    'ascii 0 is 48' => ['0', 48],
];
