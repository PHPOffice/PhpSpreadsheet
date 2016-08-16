<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    // NULL
    [
        null,
        true,
    ],
    // Boolean TRUE and NULL
    [
        true,
        null,
        true,
    ],
    // Boolean FALSE and NULL
    [
        false,
        null,
        false,
    ],
    // Both TRUE Booleans
    [
        true,
        true,
        true,
    ],
    // Mixed Booleans
    [
        true,
        false,
        false,
    ],
    // Mixed Booleans
    [
        false,
        true,
        false,
    ],
    // Both FALSE Booleans
    [
        false,
        false,
        false,
    ],
    // Multiple Mixed Booleans
    [
        true,
        true,
        false,
        false,
    ],
    // Multiple TRUE Booleans
    [
        true,
        true,
        true,
        true,
    ],
    // Multiple FALSE Booleans
    [
        false,
        false,
        false,
        false,
        false,
    ],
    [
        -1,
        -2,
        true,
    ],
    [
        0,
        0,
        false,
    ],
    [
        0,
        1,
        false,
    ],
    [
        1,
        1,
        true,
    ],
    [
        '1',
        1,
        '#VALUE!',
    ],
    // 'TRUE' String
    [
        'TRUE',
        1,
        true,
    ],
    // 'FALSE' String
    [
        'FALSE',
        true,
        false,
    ],
    // Non-numeric String
    [
        'ABCD',
        1,
        '#VALUE!',
    ],
    [
        -2,
        1,
        true,
    ],
    [
        -2,
        0,
        false,
    ],
];
