<?php

declare(strict_types=1);

return [
    [
        1234567.89,
        '1,234,567.890',
    ],
    [
        1234567.89,
        '1 234 567,890', ',', ' ',
    ],
    [
        -1234567.89,
        '-1 234 567,890', ',', ' ',
    ],
    [
        '#VALUE!',
        '1 234 567,890-', ',', ' ',
    ],
    [
        '#VALUE!',
        '1,234,567.890,123',
    ],
    [
        '#VALUE!',
        '1.234.567.890,123',
    ],
    [
        1234567.890,
        '1.234.567,890', ',', '.',
    ],
    [
        '#VALUE!',
        '1.234.567,89',
    ],
    [
        12345.6789,
        '1,234,567.89%',
    ],
    [
        123.456789,
        '1,234,567.89%%',
    ],
    [
        1.23456789,
        '1,234,567.89%%%',
    ],
    [
        '#VALUE!',
        '1,234,567.89-%',
    ],
    'no arguments' => ['exception'],
    'boolean argument' => ['#VALUE!', true],
    'slash as group separator' => [1234567.1, '1/234/567.1', '.', '/'],
    'slash as decimal separator' => [1234567.1, '1,234,567/1', '/', ','],
    'issue 3574 null string treated as 0' => [0, '', ',', ' '],
    'issue 3574 one or more spaces treated as 0' => [0, '   ', ',', ' '],
    'issue 3574 non-blank numeric string okay' => [2, ' 2 ', ',', ' '],
    'issue 3574 non-blank non-numeric string invalid' => ['#VALUE!', ' x ', ',', ' '],
];
