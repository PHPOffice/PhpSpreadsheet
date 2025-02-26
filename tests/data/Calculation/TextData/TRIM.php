<?php

declare(strict_types=1);

return [
    [
        'HELLO',
        'HELLO    ',
    ],
    [
        'HELLO',
        '    HELLO',
    ],
    [
        'HELLO',
        '   HELLO      ',
    ],
    [
        '	HELLO',
        '	HELLO',
    ],
    [
        'HELLO WORLD',
        'HELLO    WORLD',
    ],
    [
        'TRUE',
        true,
    ],
    [
        null,
        null,
    ],
    'no arguments' => ['exception'],
];
