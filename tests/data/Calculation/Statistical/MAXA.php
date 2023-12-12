<?php

declare(strict_types=1);

return [
    [
        27,
        10, 7, 9, 27, 2,
    ],
    [
        10,
        10, 7, 9, '17', 2,
    ],
    [
        0,
        -10, -7, -9, '17', -2,
    ],
    [
        1,
        -10, true, -9, '17', -2,
    ],
    [
        1,
        null, 'STRING', true, '', -2, 0, false, '27',
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
