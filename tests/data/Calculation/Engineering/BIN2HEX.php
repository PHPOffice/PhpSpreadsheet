<?php

return [
    [
        '10110010',
        'B2',
    ],
    // Too large
    [
        '111001010101',
        '#NUM!',
    ],
    // Leading places
    [
        '11111011',
        4,
        '00FB',
    ],
    // Leading places as a float
    [
        '11111011',
        3.75,
        '0FB',
    ],
    // Leading places negative
    [
        '11111011',
        -1,
        '#NUM!',
    ],
    // Leading places non-numeric
    [
        '11111011',
        'ABC',
        '#VALUE!',
    ],
    [
        '1110',
        'E',
    ],
    [
        '101',
        '5',
    ],
    [
        '10',
        '2',
    ],
    [
        '0',
        '0',
    ],
    // Invalid binary number
    [
        '21',
        '#NUM!',
    ],
    // Non string
    [
        true,
        '#VALUE!',
    ],
    // 2's Complement
    [
        '1110010101',
        'FFFFFFFF95',
    ],
    // 2's Complement
    [
        '1111111111',
        'FFFFFFFFFF',
    ],
];
