<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    // NULL
    [
        false,
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
        true,
        true,
        false,
    ],
    // Mixed Booleans
    [
        true,
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
        true,
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
        true,
        0,
        1,
    ],
    [
        true,
        1,
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
        true,
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
        true,
        -2,
        0,
    ],
];
