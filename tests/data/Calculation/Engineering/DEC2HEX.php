<?php

return [
    [
        '165',
        '357',
    ],
    [
        '54D',
        '1357',
    ],
    [
        'F6',
        '246',
    ],
    [
        '3039',
        '12345',
    ],
    [
        '75BCD15',
        '123456789',
    ],
    [
        '0064',
        '100',
        4,
    ],
    // Leading places as a float
    [
        '00064',
        '100',
        5.75,
    ],
    // Leading places negative
    [
        '#NUM!',
        '100',
        -1,
    ],
    // Leading places non-numeric
    [
        '#VALUE!',
        '100',
        'ABC',
    ],
    [
        '7B',
        '123.45',
    ],
    [
        '0',
        '0',
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
        'FFFFFFFFCA',
        '-54',
    ],
    // 2's Complement
    [
        'FFFFFFFF95',
        '-107',
    ],
];
