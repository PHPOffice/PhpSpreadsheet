<?php

return [
    [
        '144',
        '1100100',
    ],
    [
        '262',
        '10110010',
    ],
    // Too large
    [
        '#NUM!',
        '111001010101',
    ],
    // Leading places
    [
        '011',
        '1001',
        3,
    ],
    // Leading places as a float
    [
        '0011',
        '1001',
        4.75,
    ],
    // Leading places negative
    [
        '#NUM!',
        '1001',
        -1,
    ],
    // Leading places non-numeric
    [
        '#VALUE!',
        '1001',
        'ABC',
    ],
    [
        '2',
        '00000010',
    ],
    [
        '5',
        '00000101',
    ],
    [
        '15',
        '00001101',
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
        '7777777625',
        '1110010101',
    ],
    // 2's Complement
    [
        '7777777777',
        '1111111111',
    ],
];
