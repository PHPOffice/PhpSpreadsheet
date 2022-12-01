<?php

return [
    [
        7.0,
        [10, 7, 9, 2],
    ],
    [
        [5.6, '#VALUE!'],
        [10, 7, 9, 2, 'STRING VALUE'],
    ],
    [
        8.85,
        [10.5, 7.2],
    ],
    [
        43.74,
        [10.5, 7.2, 200, true, false],
    ],
    [
        0.5,
        [true, false],
    ],
    [
        0.666666666667,
        [true, false, 1],
    ],
    'no arguments' => [
        ['#DIV/0!', 'exception'],
        [],
    ],
];
