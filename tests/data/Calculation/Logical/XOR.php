<?php

return [
    // No arguments
    [
        '#VALUE!',
    ],
    [
        false,
        true, true,
    ],
    [
        true,
        true, false, false,
    ],
    [
        true,
        true, false,
    ],
    [
        true,
        false, true,
    ],
    [
        false,
        false, false,
    ],
    [
        false,
        true, true, false, false,
    ],
    [
        true,
        true, true, true, false,
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
