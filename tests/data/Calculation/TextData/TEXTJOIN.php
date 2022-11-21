<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    [
        'ABCDE,FGHIJ',
        [',', true, 'ABCDE', 'FGHIJ'],
    ],
    [
        'ABCDEFGHIJ',
        ['', true, 'ABCDE', 'FGHIJ'],
    ],
    [
        '1-2-3',
        ['-', true, 1, 2, 3],
    ],
    [
        'A-B-C-E',
        ['-', true, 'A', 'B', 'C', null, 'E'],
    ],
    [
        'A-B-C--E',
        ['-', false, 'A', 'B', 'C', null, 'E'],
    ],
    [
        'A-B-C-',
        ['-', false, 'A', 'B', 'C', null],
    ],
    [
        'A-B-C--',
        ['-', false, 'A', 'B', 'C', null, null],
    ],
    [
        '<<::>>',
        ['::', true, '<<', '>>'],
    ],
    [
        'Καλό απόγευμα',
        [' ', true, 'Καλό', 'απόγευμα'],
    ],
    [
        'Boolean-TRUE',
        ['-', true, 'Boolean', '', true],
    ],
    [
        'Boolean-TRUE',
        ['-', true, 'Boolean', '  ', true],
    ],
    [
        'Boolean--TRUE',
        ['-', false, 'Boolean', '', true],
    ],
    [
        'C:\\Users\\Mark\\Documents\\notes.doc',
        ['\\', true, 'C:', 'Users', 'Mark', 'Documents', 'notes.doc'],
    ],
    'no argument' => ['exception', []],
    'one argument' => ['exception', ['-']],
    'two arguments' => ['exception', ['-', true]],
    'three arguments' => ['a', ['-', true, 'a']],
    'boolean as string' => ['TRUE-FALSE-TRUE', ['-', true, true, false, true]],
    'result too long' => [
        '#CALC!',
        [
            ',',
            true,
            str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5),
            'abcde',
        ],
    ],
    'result just fits' => [
        str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5) . ',abcd',
        [
            ',',
            true,
            str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5),
            'abcd',
        ],
    ],
    'propagate REF' => ['#REF!', [',', true, '1', '=sheet99!A1', '3']],
    'propagate NUM' => ['#NUM!', [',', true, '1', '=SQRT(-1)', '3']],
    'propagate DIV0' => ['#DIV/0!', [',', true, '1', '=12/0', '3']],
];
