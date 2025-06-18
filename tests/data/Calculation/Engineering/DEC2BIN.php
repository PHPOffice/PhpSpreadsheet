<?php

return [
    [
        '101100101',
        357,
    ],
    // Too large
    [
        '#NUM!',
        512,
    ],
    // Too small
    [
        '#NUM!',
        -513,
    ],
    [
        '1001',
        9,
        4,
    ],
    [
        '00001001',
        9,
        8,
    ],
    // Leading places as a float
    [
        '001001',
        9,
        6.75,
    ],
    // Leading places negative
    [
        '#NUM!',
        9,
        -1,
    ],
    // Leading places non-numeric
    [
        '#VALUE!',
        9,
        'ABC',
    ],
    [
        '11110110',
        246,
    ],
    [
        '#NUM!',
        12345,
    ],
    [
        '#NUM!',
        123456789,
    ],
    [
        '1111011',
        123.45,
    ],
    [
        '0',
        0,
    ],
    // Invalid decimal
    [
        '#VALUE!',
        '3579A',
    ],
    // Non string
    [
        '#VALUE!',
        true,
    ],
    // 2's Complement
    [
        '1110011100',
        -100,
    ],
    // 2's Complement
    [
        '1110010101',
        -107,
    ],
    // 2's Complement
    [
        '1000000000',
        -512,
    ],
];
