<?php

return [
    'Single Cell' => [
        1, // Expected
        [
            [1],
        ],
        0,
    ],
    'Row Number omitted' => [
        'exception', // Expected
        [
            [1],
        ],
    ],
    'Negative Row' => [
        '#VALUE!', // Expected
        [
            [1],
            [2],
        ],
        -1,
    ],
    'Row > matrix rows' => [
        '#REF!', // Expected
        [
            [1],
            [2],
        ],
        10,
    ],
    'Row is not a number' => [
        '#NAME?', // Expected
        [
            [1],
            [2],
        ],
        'NaN',
    ],
    'Row is reference to non-number' => [
        '#VALUE!', // Expected
        [
            [1],
            [2],
        ],
        'ZZ98',
    ],
    'Row is quoted non-numeric result' => [
        '#VALUE!', // Expected
        [
            [1],
            [2],
        ],
        '"string"',
    ],
    'Row is Error' => [
        '#N/A', // Expected
        [
            [1],
            [2],
        ],
        '#N/A',
    ],
    'Return row 2 only one column' => [
        'xyz', // Expected
        [
            ['abc'],
            ['xyz'],
        ],
        2,
    ],
    'Return row 1 col 2' => [
        'def', // Expected
        [
            ['abc', 'def'],
            ['xyz', 'tuv'],
        ],
        1,
        2,
    ],
    'Column number omitted from 2-column matrix' => [
        '#REF!', // Expected
        [
            ['abc', 'def'],
            ['xyz', 'tuv'],
        ],
        1,
    ],
    'Column number omitted from 1-column matrix' => [
        'xyz', // Expected
        [
            ['abc'],
            ['xyz'],
        ],
        2,
    ],
    'Return row 2 from larger matrix (Phpspreadsheet flattens expected [2,4] to single value)' => [
        2, // Expected
        // Input
        [
            [1, 3],
            [2, 4],
        ],
        2,
        0,
    ],
    'Negative Column' => [
        '#VALUE!', // Expected
        [
            [1, 3],
            [2, 4],
        ],
        0,
        -1,
    ],
    'Column > matrix columns' => [
        '#REF!', // Expected
        [
            [1, 3],
            [2, 4],
        ],
        2,
        10,
    ],
    'Column is not a number' => [
        '#NAME?', // Expected
        [
            [1],
            [2],
        ],
        1,
        'NaN',
    ],
    'Column is reference to non-number' => [
        '#VALUE!', // Expected
        [
            [1],
            [2],
        ],
        1,
        'ZZ98',
    ],
    'Column is quoted non-number' => [
        '#VALUE!', // Expected
        [
            [1],
            [2],
        ],
        1,
        '"string"',
    ],
    'Column is Error' => [
        '#N/A', // Expected
        [
            [1],
            [2],
        ],
        1,
        '#N/A',
    ],
    'Row 2 Column 2' => [
        4, // Expected
        [
            [1, 3],
            [2, 4],
        ],
        2,
        2,
    ],
    'Row 2 Column 2 Alphabetic' => [
        'Pears',
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        2,
    ],
    'Row 2 Column 1 Alphabetic' => [
        'Bananas',
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        1,
    ],
    'Row 2 Column 0 (PhpSpreadsheet flattens result)' => [
        'Bananas',
        [
            ['Apples', 'Lemons'],
            ['Bananas', 'Pears'],
        ],
        2,
        0,
    ],
    'Row 5 column 2' => [
        3,
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        5,
        2,
    ],
    'Row 5 column 0 (flattened)' => [
        8,
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        5,
        0,
    ],
    'Row 0 column 2 (flattened)' => [
        6,
        [
            [4, 6],
            [5, 3],
            [6, 9],
            [7, 5],
            [8, 3],
        ],
        0,
        2,
    ],
];
