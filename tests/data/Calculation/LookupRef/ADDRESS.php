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
        'R2C[3]',
        2,
        3,
        2,
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
        1,
        false,
        'EXCEL SHEET',
    ],
    [
        '#VALUE!',
        -2,
        -2,
    ]
];
