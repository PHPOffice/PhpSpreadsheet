<?php

declare(strict_types=1);

return [
    [
        '',
        null,
        1,
        1,
    ],
    [
        '',
        '',
        1,
        1,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        0,
        1,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        5,
        -1,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        'NaN',
        1,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        2,
        'NaN',
    ],
    'length null treated as zero' => [
        '',
        'QWERTYUIOP',
        2,
        null,
    ],
    'length not specified' => [
        'exception',
        'QWERTYUIOP',
        5,
    ],
    'start not specified' => [
        'exception',
        'QWERTYUIOP',
    ],
    'string not specified' => [
        'exception',
    ],
    [
        'IOP',
        'QWERTYUIOP',
        8,
        20,
    ],
    [
        '',
        'QWERTYUIOP',
        999,
        2,
    ],
    [
        'DEF',
        'ABCDEFGHI',
        4,
        3,
    ],
    [
        'δύο',
        'Ενα δύο τρία τέσσερα πέντε',
        5,
        3,
    ],
    [
        'δύο τρία',
        'Ενα δύο τρία τέσσερα πέντε',
        5,
        8,
    ],
    [
        'τρία τέσσερα',
        'Ενα δύο τρία τέσσερα πέντε',
        9,
        12,
    ],
    [
        'R',
        true,
        2,
        1,
    ],
    [
        'AL',
        false,
        2,
        2,
    ],
];
