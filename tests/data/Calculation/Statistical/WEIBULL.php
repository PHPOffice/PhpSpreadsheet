<?php

return [
    [
        0.929581390070,
        105, 20, 100, true,
    ],
    [
        0.035588864025,
        105, 20, 100, false,
    ],
    'too few arguments' => [
        'exception',
        105, 20, 100,
    ],
    [
        1.10363832351433,
        1, 3, 1, false,
    ],
    [
        0.985212776817482,
        2, 5, 1.5, true,
    ],
    [
        '#VALUE!',
        'NaN', 5, 1.5, true,
    ],
    [
        '#VALUE!',
        2, 'NaN', 1.5, true,
    ],
    [
        '#VALUE!',
        2, 5, 'NaN', true,
    ],
    [
        '#VALUE!',
        2, 5, 1.5, 'NaN',
    ],
    [
        '#NUM!',
        -2, 5, 1.5, true,
    ],
    [
        '#NUM!',
        -2, 0, 1.5, true,
    ],
    [
        '#NUM!',
        -2, 5, 0, true,
    ],
];
