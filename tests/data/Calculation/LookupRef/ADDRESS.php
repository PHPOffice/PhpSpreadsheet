<?php

return [
    [
        '$C$2',
        2,
        3,
    ],
    [
        'C$2',
        2,
        3,
        2,
    ],
    [
        '$C2',
        2,
        3,
        3,
    ],
    [
        'R2C[3]',
        2,
        3,
        2,
        false,
    ],
    [
        'R[2]C3',
        2,
        3,
        3,
        false,
    ],
    [
        "'[Book1]Sheet1'!R2C3",
        2,
        3,
        1,
        false,
        '[Book1]Sheet1',
    ],
    [
        "'EXCEL SHEET'!R2C3",
        2,
        3,
        null,
        false,
        'EXCEL SHEET',
    ],
    [
        "'EXCEL SHEET'!\$C\$2",
        2,
        3,
        1,
        null,
        'EXCEL SHEET',
    ],
    [
        '#VALUE!',
        -2,
        -2,
    ],
];
