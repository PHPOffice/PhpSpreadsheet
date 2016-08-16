<?php

return [
    [
        357,
        '101100101',
    ],
    // Too large
    [
        512,
        '#NUM!',
    ],
    // Too small
    [
        -513,
        '#NUM!',
    ],
    [
        9,
        4,
        '1001',
    ],
    [
        9,
        8,
        '00001001',
    ],
    // Leading places as a float
    [
        9,
        6.75,
        '001001',
    ],
    // Leading places negative
    [
        9,
        -1,
        '#NUM!',
    ],
    // Leading places non-numeric
    [
        9,
        'ABC',
        '#VALUE!',
    ],
    [
        246,
        '11110110',
    ],
    [
        12345,
        '#NUM!',
    ],
    [
        123456789,
        '#NUM!',
    ],
    [
        123.45,
        '1111011',
    ],
    [
        0,
        '0',
    ],
    // Invalid decimal
    [
        '3579A',
        '#VALUE!',
    ],
    // Non string
    [
        true,
        '#VALUE!',
    ],
    // 2's Complement
    [
        -100,
        '1110011100',
    ],
    // 2's Complement
    [
        -107,
        '1110010101',
    ],
    // 2's Complement
    [
        -512,
        '1000000000',
    ],
];
