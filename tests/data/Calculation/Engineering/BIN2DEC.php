<?php

return [
    [
        '10110010',
        '178',
    ],
    [
        '1100100',
        '100',
    ],
    // Too large
    [
        '111001010101',
        '#NUM!',
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
        '-107',
    ],
    // 2's Complement
    [
        '1111111111',
        '-1',
    ],
];
