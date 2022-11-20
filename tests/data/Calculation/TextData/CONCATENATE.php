<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    [
        'ABCDEFGHIJ',
        'ABCDE',
        'FGHIJ',
    ],
    [
        '123',
        1,
        2,
        3,
    ],
    [
        'Boolean-TRUE',
        'Boolean',
        '-',
        true,
    ],
    'no arguments' => ['exception'],
    'result just fits' => [
        // Note use Armenian character below to make sure chars, not bytes
        str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5) . 'ABCDE',
        str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5),
        'ABCDE',
    ],
    'result too long' => [
        '#CALC!',
        str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5),
        'abc',
        '=A2',
    ],
    'propagate DIV0' => ['#DIV/0!', '1', '=2/0', '3'],
];
