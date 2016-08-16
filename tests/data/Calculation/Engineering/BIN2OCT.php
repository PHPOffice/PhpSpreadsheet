<?php

return [
    [
        '1100100',
        '144',
    ],
    [
        '10110010',
        '262',
    ],
    // Too large
    [
        '111001010101',
        '#NUM!',
    ],
    // Leading places
    [
        '1001',
        3,
        '011',
    ],
    // Leading places as a float
    [
        '1001',
        4.75,
        '0011',
    ],
    // Leading places negative
    [
        '1001',
        -1,
        '#NUM!',
    ],
    // Leading places non-numeric
    [
        '1001',
        'ABC',
        '#VALUE!',
    ],
    [
        '00000010',
        '2',
    ],
    [
        '00000101',
        '5',
    ],
    [
        '00001101',
        '15',
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
        '7777777625',
    ],
    // 2's Complement
    [
        '1111111111',
        '7777777777',
    ],
];
