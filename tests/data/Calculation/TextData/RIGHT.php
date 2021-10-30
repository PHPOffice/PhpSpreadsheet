<?php

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
        'P',
        'QWERTYUIOP',
    ],
    [
        'GHI',
        'ABCDEFGHI',
        3,
    ],
    [
        '',
        'ABCDEFGHI',
        0,
    ],
    [
        'πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
        5,
    ],
    [
        'τέσσερα πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
        13,
    ],
    [
        'τρία τέσσερα πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
        18,
    ],
    [
        'UE',
        true,
        2,
    ],
    [
        'SE',
        false,
        2,
    ],
    'string not specified' => [
        'exception',
    ],
];
