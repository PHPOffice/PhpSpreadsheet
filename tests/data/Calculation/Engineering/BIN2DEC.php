<?php

return [
    [
        '178',
        '10110010',
    ],
    [
        '100',
        '1100100',
    ],
    // Too large
    [
        '#NUM!',
        '111001010101',
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
        '-107',
        '1110010101',
    ],
    // 2's Complement
    [
        '-1',
        '1111111111',
    ],
];
