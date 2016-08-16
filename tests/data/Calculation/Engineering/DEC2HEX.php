<?php

return [
    [
        '357',
        '165',
    ],
    [
        '1357',
        '54D',
    ],
    [
        '246',
        'F6',
    ],
    [
        '12345',
        '3039',
    ],
    [
        '123456789',
        '75BCD15',
    ],
    [
        '100',
        4,
        '0064',
    ],
    // Leading places as a float
    [
        '100',
        5.75,
        '00064',
    ],
    // Leading places negative
    [
        '100',
        -1,
        '#NUM!',
    ],
    // Leading places non-numeric
    [
        '100',
        'ABC',
        '#VALUE!',
    ],
    [
        '123.45',
        '7B',
    ],
    [
        '0',
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
        '-54',
        'FFFFFFFFCA',
    ],
    // 2's Complement
    [
        '-107',
        'FFFFFFFF95',
    ],
];
