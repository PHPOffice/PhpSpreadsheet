<?php

return [
    [
        2,
        19,
        17,
    ],
    [
        -7,
        19,
        -13,
    ],
    [
        0,
        34,
        17,
    ],
    [
        '#DIV/0!',
        34,
        0,
    ],
    [
        1,
        3,
        2,
    ],
    [
        1,
        -3,
        2,
    ],
    [
        -1,
        3,
        -2,
    ],
    [
        -1,
        -3,
        -2,
    ],
    [
        1.2,
        2.5,
        1.3,
    ],
    [
        '#VALUE!', // had been 0, which was wrong
        '',
        1,
    ],
    [0, null, 5],
    [0, false, 5],
    [1, true, 5],
    ['#VALUE!', 'XYZ', 2],
    ['#VALUE!', 2, 'XYZ'],
    ['exception', 2],
    ['exception'],
];
