<?php

declare(strict_types=1);

return [
    [
        2,
        10, 7, 2, 9, 27,
    ],
    [
        -9,
        10, 7, -9, '-27', 2,
    ],
    [
        0,
        null, 'STRING', true, '', '27',
    ],
    'error among arguments' => [
        '#DIV/0!',
        1, 3, '=5/0', -2,
    ],
];
