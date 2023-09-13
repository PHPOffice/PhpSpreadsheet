<?php

declare(strict_types=1);

return [
    'no arguments' => [
        'exception',
    ],
    'only argument is null reference' => [
        '#VALUE!',
        null,
    ],
    [
        false,
        true, true,
    ],
    [
        true,
        true, false, false,
    ],
    [
        true,
        true, false,
    ],
    [
        true,
        false, true,
    ],
    [
        false,
        false, false,
    ],
    [
        false,
        true, true, false, false,
    ],
    [
        true,
        true, true, true, false,
    ],
    'ignore string other two should be true' => [
        false,
        'TRUE',
        1,
        0.5,
    ],
    [
        true,
        'FALSE',
        1.5,
        0,
    ],
    'only arg is string' => [
        '#VALUE!',
        'HELLO WORLD',
    ],
    'true string is ignored' => [
        true,
        'TRUE',
        1,
    ],
    'false string is ignored' => [
        true,
        'FALSE',
        true,
    ],
    'string 1 is ignored' => [
        true,
        '1',
        true,
    ],
    'non-boolean string is ignored' => [
        true,
        'ABCD',
        1,
    ],
];
