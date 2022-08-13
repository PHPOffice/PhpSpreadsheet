<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    'END Case-sensitive Offset 1' => [
        "'s red hood",
        [
            "Red riding hood's red hood",
            'hood',
        ],
    ],
    'END Case-sensitive Offset 2' => [
        '',
        [
            "Red riding hood's red hood",
            'hood',
            2,
        ],
    ],
    'END Case-sensitive Offset -1' => [
        '',
        [
            "Red riding hood's red hood",
            'hood',
            -1,
        ],
    ],
    'END Case-sensitive Offset -2' => [
        "'s red hood",
        [
            "Red riding hood's red hood",
            'hood',
            -2,
        ],
    ],
    'END Case-sensitive Offset 3' => [
        ExcelError::NA(),
        [
            "Red riding hood's red hood",
            'hood',
            3,
        ],
    ],
    'END Case-sensitive Offset -3' => [
        ExcelError::NA(),
        [
            "Red riding hood's red hood",
            'hood',
            -3,
        ],
    ],
    'END Case-sensitive Offset 3 with end' => [
        '',
        [
            "Red riding hood's red hood",
            'hood',
            3,
            0,
            1,
        ],
    ],
    'END Case-sensitive Offset -3 with end' => [
        "Red riding hood's red hood",
        [
            "Red riding hood's red hood",
            'hood',
            -3,
            0,
            1,
        ],
    ],
    'END Case-sensitive - No Match' => [
        ExcelError::NA(),
        [
            "Red riding hood's red hood",
            'HOOD',
        ],
    ],
    'END Case-insensitive Offset 1' => [
        "'s red hood",
        [
            "Red riding hood's red hood",
            'HOOD',
            1,
            1,
        ],
    ],
    'END Case-insensitive Offset 2' => [
        '',
        [
            "Red riding hood's red hood",
            'HOOD',
            2,
            1,
        ],
    ],
    'END Offset 0' => [
        ExcelError::VALUE(),
        [
            "Red riding hood's red hood",
            'hood',
            0,
        ],
    ],
    'Empty match positive' => [
        "Red riding hood's red hood",
        [
            "Red riding hood's red hood",
            '',
        ],
    ],
    'Empty match negative' => [
        '',
        [
            "Red riding hood's red hood",
            '',
            -1,
        ],
    ],
    'START Case-sensitive Offset 1' => [
        ' riding hood',
        [
            "Red Riding Hood's red riding hood",
            'red',
        ],
    ],
    'START Case-insensitive Offset 1' => [
        " Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            1,
            1,
        ],
    ],
    'START Case-sensitive Offset -2' => [
        "Red Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            -2,
            0,
            1,
        ],
    ],
    'START Case-insensitive Offset -2' => [
        " Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            -2,
            1,
            1,
        ],
    ],
    [
        ' riding hood',
        [
            "Red Riding Hood's red riding hood",
            'red',
            1,
            0,
        ],
    ],
    [
        " Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            1,
            1,
        ],
    ],
    [
        "Red Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            -2,
            0,
            1,
        ],
    ],
    [
        " Riding Hood's red riding hood",
        [
            "Red Riding Hood's red riding hood",
            'red',
            -2,
            1,
            1,
        ],
    ],
    [
        ExcelError::NA(),
        [
            'Socrates',
            ' ',
            1,
            0,
            0,
        ],
    ],
    [
        '',
        [
            'Socrates',
            ' ',
            1,
            0,
            1,
        ],
    ],
    'Multi-delimiter Case-Insensitive Offset 1' => [
        " riding hood's red riding hood",
        [
            "Little Red riding hood's red riding hood",
            ['HOOD', 'RED'],
            1,
            1,
        ],
    ],
    'Multi-delimiter Case-Insensitive Offset 2' => [
        "'s red riding hood",
        [
            "Little Red riding hood's red riding hood",
            ['HOOD', 'RED'],
            2,
            1,
        ],
    ],
    'Multi-delimiter Case-Insensitive Offset 3' => [
        ' riding hood',
        [
            "Little Red riding hood's red riding hood",
            ['HOOD', 'RED'],
            3,
            1,
        ],
    ],
    'Multi-delimiter Case-Insensitive Offset -2' => [
        ' riding hood',
        [
            "Little Red riding hood's red riding hood",
            ['HOOD', 'RED'],
            -2,
            1,
        ],
    ],
];
