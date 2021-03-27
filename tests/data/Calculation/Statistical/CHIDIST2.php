<?php

return [
    [
        0.520499877813,
        0.5, 1, true,
    ],
    [
        0.207553748710,
        2, 3, false,
    ],
    [
        0.111565080074,
        3, 2, false,
    ],
    [
        0.776869839852,
        3, 2, true,
    ],
    [
        '#VALUE!',
        'NaN', 3, true,
    ],
    [
        '#VALUE!',
        2, 'NaN', true,
    ],
    [
        '#VALUE!',
        2, 3, 'NaN',
    ],
    'Value < 0' => [
        '#NUM!',
        -8, 3, true,
    ],
    'Degrees < 1' => [
        '#NUM!',
        8, 0, true,
    ],
];
