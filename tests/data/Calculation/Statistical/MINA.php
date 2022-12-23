<?php

return [
    [
        2,
        10, 7, 9, 27, 2,
    ],
    [
        -7,
        10, '-9', -7, '17', 2,
    ],
    [
        0,
        10, 7, 9, '17', 2,
    ],
    [
        1,
        10, true, 9, 2,
    ],
    [
        0,
        null, true, 2, false,
    ],
    [
        0,
        null, 'STRING', '', 'xl95',
    ],
    [
        0,
        null, null, null, null,
    ],
    'error among arguments' => [
        '#DIV/0!',
        1, 3, '=5/0', -2,
    ],
];
