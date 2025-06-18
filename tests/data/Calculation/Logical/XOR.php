<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    [
        false,
        1 > 0, 2 > 0,
    ],
    [
        true,
        true, false, false,
    ],
    [
        true,
        1 > 0, 0 > 1,
    ],
    [
        true,
        0 > 1, 2 > 0,
    ],
    [
        false,
        0 > 1, 0 > 2,
    ],
    [
        false,
        1 > 0, 2 > 0, 0 > 1, 0 > 2,
    ],
    [
        true,
        1 > 0, 2 > 0, 3 > 0, 0 > 1,
    ],
    [
        false,
        'TRUE',
        1,
        0.5,
    ],
    [
        true,
        'FALSE',
        1.5,
        0,
    ],
    [
        '#VALUE!',
        'HELLO WORLD',
    ],
];
