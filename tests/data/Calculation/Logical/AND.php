<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    // NULL
    [
        true,
        null,
    ],
    // Boolean TRUE and NULL
    [
        true,
        true,
        null,
    ],
    // Boolean FALSE and NULL
    [
        false,
        false,
        null,
    ],
    // Both TRUE Booleans
    [
        true,
        true,
        true,
    ],
    // Mixed Booleans
    [
        false,
        true,
        false,
    ],
    // Mixed Booleans
    [
        false,
        false,
        true,
    ],
    // Both FALSE Booleans
    [
        false,
        false,
        false,
    ],
    // Multiple Mixed Booleans
    [
        false,
        true,
        true,
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
        true,
        -1,
        -2,
    ],
    [
        false,
        0,
        0,
    ],
    [
        false,
        0,
        1,
    ],
    [
        true,
        1,
        1,
    ],
    [
        '#VALUE!',
        '1',
        1,
    ],
    // 'TRUE' String
    [
        true,
        'TRUE',
        1,
    ],
    // 'FALSE' String
    [
        false,
        'FALSE',
        true,
    ],
    // Non-numeric String
    [
        '#VALUE!',
        'ABCD',
        1,
    ],
    [
        true,
        -2,
        1,
    ],
    [
        false,
        -2,
        0,
    ],
];
