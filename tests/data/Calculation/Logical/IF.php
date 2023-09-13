<?php

declare(strict_types=1);

return [
    'no arguments' => [
        'exception',
    ],
    '1 argument true' => [
        'exception',
        true,
    ],
    '1 argument false' => [
        'exception',
        false,
    ],
    'value_if_false omitted condtion is true' => [
        'ABC',
        true,
        'ABC',
    ],
    'value_if_false omitted condition is false' => [
        false,
        false,
        'ABC',
    ],
    'value_if_true omitted condition is true' => [0, true, null, 'error'],
    'value_if_true omitted condition is false' => ['error', false, null, 'error'],
    [
        'ABC',
        true,
        'ABC',
        'XYZ',
    ],
    [
        'XYZ',
        false,
        'ABC',
        'XYZ',
    ],
    [
        '#N/A',
        '#N/A',
        'ABC',
        'XYZ',
    ],
];
