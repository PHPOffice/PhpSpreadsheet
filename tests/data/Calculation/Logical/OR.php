<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    // NULL
    [
        null,
        false,
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
        true,
    ],
    // Mixed Booleans
    [
        false,
        true,
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
        false,
        true,
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
        true,
    ],
    [
        1,
        1,
        true,
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
        true,
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
        true,
    ],
];
