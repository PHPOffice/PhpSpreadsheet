<?php

return [
    // Must be C
    [
        'C',
        'A',
        'A',
        'C',
        'B',
        'D',
        '??',
    ],
    // Must be Female
    [
        'Female',
        2,
        '1',
        'Male',
        '2',
        'Female',
    ],
    // Must be X using default
    [
        'X',
        'U',
        'ABC',
        'Y',
        'DEF',
        'Z',
        'X',
    ],
    // Must be N/A default value not defined
    [
        '#N/A',
        'U',
        'ABC',
        'Y',
        'DEF',
        'Z',
    ],
    'Array return' => [
        [[4, 5, 6]],
        2,
        1,
        [[1, 2, 3]],
        2,
        [[4, 5, 6]],
        [[7, 8, 9]],
    ],
    'Array return as default' => [
        [[7, 8, 9]],
        3,
        1,
        [[1, 2, 3]],
        2,
        [[4, 5, 6]],
        [[7, 8, 9]],
    ],
    // Must be value - no parameter
    [
        '#VALUE!',
    ],
];
