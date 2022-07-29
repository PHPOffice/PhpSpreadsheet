<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [
        [['Hello', 'World']],
        [
            'Hello World',
            ' ',
            '',
        ],
    ],
    [
        [['Hello'], ['World']],
        [
            'Hello World',
            '',
            ' ',
        ],
    ],
    [
        [['To', 'be', 'or', 'not', 'to', 'be']],
        [
            'To be or not to be',
            ' ',
            '',
        ],
    ],
    [
        [
            ['1', '2', '3'],
            ['4', '5', '6'],
        ],
        [
            '1,2,3;4,5,6',
            ',',
            ';',
        ],
    ],
    [
        [
            ['Do', ' Or do not', ' There is no try', ' ', 'Anonymous'],
        ],
        [
            'Do. Or do not. There is no try. -Anonymous',
            ['.', '-'],
            '',
        ],
    ],
    [
        [['Do'], [' Or do not'], [' There is no try'], [' '], ['Anonymous']],
        [
            'Do. Or do not. There is no try. -Anonymous',
            '',
            ['.', '-'],
        ],
    ],
    [
        [
            ['Do', ' Or do not', ' There is no try', ' '],
            ['Anonymous', ExcelError::NA(), ExcelError::NA(), ExcelError::NA()],
        ],
        [
            'Do. Or do not. There is no try. -Anonymous',
            '.',
            '-',
        ],
    ],
    [
        [
            ['', '', '1'],
            ['', '', ExcelError::NA()],
            ['', '2', ''],
            ['3', ExcelError::NA(), ExcelError::NA()],
            ['', ExcelError::NA(), ExcelError::NA()],
            ['', '4', ExcelError::NA()],
        ],
        [
            '--1|-|-2-|3||-4',
            '-',
            '|',
        ],
    ],
    [
        [
            ['1'],
            ['2'],
            ['3'],
            ['4'],
        ],
        [
            '--1|-|-2-|3||-4',
            '-',
            '|',
            true,
        ],
    ],
    [
        [['', 'BCD', 'FGH', 'JKLMN', 'PQRST', 'VWXYZ']],
        [
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ['A', 'E', 'I', 'O', 'U'],
            '',
        ],
    ],
];
