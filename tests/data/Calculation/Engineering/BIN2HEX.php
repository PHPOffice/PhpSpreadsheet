<?php

return [
    [
        'B2',
        '10110010',
    ],
    // Too large
    [
        '#NUM!',
        '111001010101',
    ],
    // Leading places
    [
        '00FB',
        '11111011',
        4,
    ],
    // Leading places as a float
    [
        '0FB',
        '11111011',
        3.75,
    ],
    // Leading places negative
    [
        '#NUM!',
        '11111011',
        -1,
    ],
    // Leading places non-numeric
    [
        '#VALUE!',
        '11111011',
        'ABC',
    ],
    [
        'E',
        '1110',
    ],
    [
        '5',
        '101',
    ],
    [
        '2',
        '10',
    ],
    [
        '0',
        '0',
    ],
    // Invalid binary number
    [
        '#NUM!',
        '21',
    ],
    // Non string
    [
        '#VALUE!',
        true,
    ],
    // 2's Complement
    [
        'FFFFFFFF95',
        '1110010101',
    ],
    // 2's Complement
    [
        'FFFFFFFFFF',
        '1111111111',
    ],
];
