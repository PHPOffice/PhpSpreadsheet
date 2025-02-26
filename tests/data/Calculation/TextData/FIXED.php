<?php

declare(strict_types=1);

return [
    [
        '123,456.79',
        123456.789,
        2,
        false,
    ],
    [
        '123456.8',
        123456.789,
        1,
        true,
    ],
    [
        '123456.79',
        123456.789,
        2,
        true,
    ],
    [
        '-123456.79',
        -123456.789,
        2,
        true,
    ],
    [
        '123500',
        123456.789,
        -2,
        true,
    ],
    [
        '123,500',
        123456.789,
        -2,
    ],
    [
        '-123500',
        -123456.789,
        -2,
        true,
    ],
    [
        '-123,500',
        -123456.789,
        -2,
    ],
    [
        '#VALUE!',
        'ABC',
        2,
        null,
    ],
    [
        '#VALUE!',
        123.456,
        'ABC',
        null,
    ],
    'no arguments' => ['exception'],
    'just one argument is okay' => ['123.00', 123],
    'null second argument' => ['123', 123, null],
    'false second argument' => ['123', 123, false],
    'true second argument' => ['123.0', 123, true],
];
