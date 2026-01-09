<?php

declare(strict_types=1);

return [
    [
        'null',
        null,
    ],
    [
        'e',
        '#NULL!',
    ],
    [
        'b',
        false,
    ],
    [
        'b',
        true,
    ],
    [
        's',
        'FALSE',
    ],
    [
        's',
        'TRUE',
    ],
    [
        's',
        '',
    ],
    [
        's',
        'ABC',
    ],
    [
        'n',
        '123',
    ],
    [
        'n',
        123,
    ],
    [
        'n',
        0.123,
    ],
    [
        'n',
        '-123',
    ],
    [
        'n',
        '1.23E4',
    ],
    [
        'n',
        '-1.23E4',
    ],
    [
        'n',
        '1.23E-4',
    ],
    [
        's',
        '000123',
    ],
    [
        'f',
        '=123',
    ],
    [
        'e',
        '#DIV/0!',
    ],
    [
        's',
        '123456\n',
    ],
    'Numeric that exceeds PHP MAX_INT Size' => [
        's',
        '1234567890123459012345689012345690',
    ],
    'Issue 1310 Multiple = at start' => ['s', '======'],
    'Issue 1310 Variant 1' => ['s', '= ====='],
    'Issue 1310 Variant 2' => ['s', '=2*3='],
    'Issue 4766 very large positive exponent treated as string' => ['s', '4E433'],
    'Issue 4766 very large negative exponent numeric' => ['n', '4E-433'],
    'Issue 4766 small exponent no decimal point numeric' => ['n', '4E4'],
];
