<?php

declare(strict_types=1);

return [
    [
        'A',
        1,
    ],
    [
        'Z',
        26,
    ],
    [
        'AA',
        27,
    ],
    [
        'AB',
        28,
    ],
    [
        'AZ',
        52,
    ],
    [
        'BA',
        53,
    ],
    [
        'BZ',
        78,
    ],
    [
        'CA',
        79,
    ],
    [
        'IV',
        256,
    ],
    [
        'ZZ',
        702,
    ],
    [
        'AAA',
        703,
    ],
    [
        'BAA',
        1379,
    ],
    'zero not allowed' => ['exception', 0],
    'negative not allowed' => ['exception', -1],
    'maximum possible' => ['XFD', 16384],
    'beyond maximum possible' => ['exception', 16385],
];
