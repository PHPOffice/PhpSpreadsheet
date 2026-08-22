<?php

declare(strict_types=1);

return [
    'empty cell treated as 0' => [
        0,
        null,
        'Error',
    ],
    [
        true,
        true,
        'Error',
    ],
    [
        42,
        42,
        'Error',
    ],
    [
        '',
        '',
        'Error',
    ],
    [
        'ABC',
        'ABC',
        'Error',
    ],
    [
        'Error',
        '#VALUE!',
        'Error',
    ],
    [
        'Error',
        '#NAME?',
        'Error',
    ],
    [
        'Error',
        '#N/A',
        'Error',
    ],
];
