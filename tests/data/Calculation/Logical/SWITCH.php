<?php

return [
    'match value1 A result is C' => [
        'C',
        'A',
        'A',
        'C',
        'B',
        'D',
        '??',
    ],
    'match value2 2 result is female' => [
        'Female',
        2,
        '1',
        'Male',
        '2',
        'Female',
    ],
    'defined default value' => [
        'X',
        'U',
        'ABC',
        'Y',
        'DEF',
        'Z',
        'X',
    ],
    'undefined default value' => [
        '#N/A',
        'U',
        'ABC',
        'Y',
        'DEF',
        'Z',
    ],
    'no arguments' => ['exception'],
];
