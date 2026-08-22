<?php

declare(strict_types=1);

return [
    [
        '',
        null,
        1,
    ],
    [
        '',
        '',
        1,
    ],
    [
        '',
        'ABC',
        0,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        -1,
    ],
    [
        '#VALUE!',
        'QWERTYUIOP',
        'NaN',
    ],
    'null length defaults to 0' => [
        '',
        'QWERTYUIOP',
        null,
    ],
    'omitted length defaults to 1' => [
        'Q',
        'QWERTYUIOP',
    ],
    [
        'ABC',
        'ABCDEFGHI',
        3,
    ],
    [
        'Ενα',
        'Ενα δύο τρία τέσσερα πέντε',
        3,
    ],
    [
        'Ενα δύο',
        'Ενα δύο τρία τέσσερα πέντε',
        7,
    ],
    [
        'Ενα δύο τρία',
        'Ενα δύο τρία τέσσερα πέντε',
        12,
    ],
    [
        'TR',
        true,
        2,
    ],
    [
        'FA',
        false,
        2,
    ],
    'string not specified' => [
        'exception',
    ],
];
