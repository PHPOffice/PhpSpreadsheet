<?php

declare(strict_types=1);

return [
    'unsorted numeric array still finds match with type 1?' => [
        2, // Expected
        2, // Input
        [2, 0, 4, 3],
        1,
    ],
    'unsorted array still finds match with type 1?' => [
        6,
        'Amplitude',
        ['Aardvark', 'Apple', 'A~*e', 'A*e', 'A[solve', 'Amplitude', 'Adverse', 'Apartment'],
        1,
    ],
    // Third argument = 0
    [
        1, // Expected
        2, // Input
        [2, 3, 4, 3],
        0,
    ],
    [
        '#N/A', // Expected
        2, // Input
        [1, 0, 4, 3],
        0,
    ],
    [
        1, // Expected
        2, // Input
        [2, 0, 0, 3],
        0,
    ],
    [
        2, // Expected
        0, // Input
        [2, 0, 0, 3],
        0,
    ],
    // Third argument = 1
    [
        1, // Expected
        2, // Input
        [2, 3, 4, 3],
        1,
    ],
    [
        3, // Expected
        2, // Input
        [2, 0, 0, 3],
        1,
    ],
    [
        4, // Expected
        4, // Input
        [2, 0, 0, 3],
        1,
    ],
    // Third argument = -1
    [
        1, // Expected
        2, // Input
        [2, 0, 0, 3],
        -1,
    ],
    [
        4, // Expected
        2, // Input
        [3, 3, 4, 5],
        -1,
    ],
    [
        1, // Expected
        5, // Input
        [8, 4, 3, 2],
        -1,
    ],
    [
        '#N/A', // Expected
        6, // Input
        [3, 5, 6, 8],
        -1,
    ],
    [
        1, // Expected
        6, // Input
        [8, 5, 4, 2],
        -1,
    ],
    [
        3, // Expected
        4, // Input
        [5, 8, 4, 2],
        -1,
    ],
    [
        2, // Expected
        4, // Input
        [8, 8, 3, 2],
        -1,
    ],
    [ // Default matchtype
        4, // Expected
        4, // Input
        [2, 0, 0, 3],
        null,
    ],
    // match on ranges with empty cells
    [
        3, // Expected
        4, // Input
        [1, null, 4, null, 8],
        1,
    ],
    [
        3, // Expected
        5, // Input
        [1, null, 4, null, null],
        1,
    ],
    // 0s are causing errors, because things like 0 == 'x' is true. Thanks PHP!
    '0 does not match x' => [
        3,
        'x',
        [0, 0, 'x', 'x', 'x'],
        0,
    ],
    [
        2,
        'a',
        [false, 'a', 1],
        -1,
    ],
    [
        '#N/A', // Expected
        0,
        ['x', true, false],
        -1,
    ],
    [
        '#N/A', // Expected
        true,
        ['a', 'b', 'c'],
        -1,
    ],
    [
        '#N/A', // Expected
        true,
        [0, 1, 2],
        -1,
    ],
    [
        '#N/A', // Expected
        true,
        [0, 1, 2],
        0,
    ],
    [
        '#N/A', // Expected
        true,
        [0, 1, 2],
        1,
    ],
    [
        1, // Expected
        true,
        [true, true, true],
        -1,
    ],
    [
        1, // Expected
        true,
        [true, true, true],
        0,
    ],
    [
        3, // Expected
        true,
        [true, true, true],
        1,
    ],
    // lookup stops when value < searched one
    [
        5, // Expected
        6,
        [true, false, 'a', 'z', 222222, 2, 99999999],
        -1,
    ],
    // when mixing numeric types
    [
        4, // Expected
        4.6,
        [1, 2, 3, 4, 5],
        1,
    ],
    [
        4, // Expected
        4,
        [1, 2, 3, 3.8, 5],
        1,
    ],
    // if element of same data type met and it is < than searched one #N/A - no further processing
    [
        '#N/A', // Expected
        6,
        [true, false, 'a', 'z', 2, 888],
        -1,
    ],
    'number compared to string type -1' => [
        '#N/A', // Expected
        6,
        ['6'],
        -1,
    ],
    'number compared to string type 0' => [
        '#N/A', // Expected
        6,
        ['6'],
        0,
    ],
    'number compared to string type 1' => [
        '#N/A', // Expected
        6,
        ['6'],
        1,
    ],
    'string compared to number type -1' => [
        '#N/A', // Expected
        '6',
        [6],
        -1,
    ],
    'string compared to number type 0' => [
        '#N/A', // Expected
        '6',
        [6],
        0,
    ],
    'string compared to number type 1' => [
        '#N/A', // Expected
        '6',
        [6],
        1,
    ],
    // expression match
    [
        2, // Expected
        'a?b',
        ['a', 'abb', 'axc'],
        0,
    ],
    [
        1, // Expected
        'a*',
        ['aAAAAAAA', 'as', 'az'],
        0,
    ],
    [
        3, // Expected
        '1*11*1',
        ['abc', 'efh', '1a11b1'],
        0,
    ],
    [
        3, // Expected
        '1*11*1',
        ['abc', 'efh', '1a11b1'],
        0,
    ],
    [
        2, // Expected
        'a*~*c',
        ['aAAAAA', 'a123456*c', 'az', 'alembic'],
        0,
    ],
    [
        3, // Expected
        'a*123*b',
        ['aAAAAA', 'a123456*c', 'a99999123b'],
        0,
    ],
    [
        1, // Expected
        '*',
        ['aAAAAA', 'a111123456*c', 'qq'],
        0,
    ],
    [
        2, // Expected
        '?',
        ['aAAAAA', 'a', 'a99999123b'],
        0,
    ],
    [
        '#N/A', // Expected
        '?',
        [1, 22, 333],
        0,
    ],
    [
        3, // Expected
        '???',
        [1, 22, 'aaa'],
        0,
    ],
    [
        3, // Expected
        '*',
        [1, 22, 'aaa'],
        0,
    ],
    [
        '#N/A', // Expected
        'abc',
        [1, 22, 'aaa'],
        0,
    ],
    [
        '#N/A', // Expected (Invalid lookup value)
        new DateTime('2021-03-11'),
        [1, 22, 'aaa'],
        1,
    ],
    'int match type other than 0/1/-1 is accepted' => [
        3, // Expected (Invalid match type)
        'abc',
        [1, 22, 'aaa'],
        123,
    ],
    'float match type is accepted' => [
        3, // Expected (Invalid match type)
        'abc',
        [1, 22, 'aaa'],
        123.5,
    ],
    'numeric string match type is accepted' => [
        3, // Expected (Invalid match type)
        'abc',
        [1, 22, 'aaa'],
        '89.7',
    ],
    'non-numeric string match type is not accepted' => [
        '#VALUE!', // Expected (Invalid match type)
        'abc',
        [1, 22, 'aaa'],
        '"x"',
    ],
    'empty lookup array' => [
        '#N/A', // Expected (Empty lookup array)
        'abc',
        [],
        1,
    ],
    'wildcard match 1 with type -1' => [
        [8, 2], // LibreOffice matches wildcard but shouldn't
        'A*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        -1,
    ],
    [
        2,
        'A*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        0,
    ],
    'wildcard match with tilde' => [
        4,
        'A~*e',
        ['Aardvark', 'Apple', 'A~*e', 'A*e', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        0,
    ],
    'string with preg_quote escaped character' => [
        5,
        'A[solve',
        ['Aardvark', 'Apple', 'A~*e', 'A*e', 'A[solve', 'Amplitude', 'Adverse', 'Apartment'],
        0,
    ],
    // duplicate test deleted - see 'wildcard with type 1' below
    // end of deletions
    'wildcard match 2 with type -1' => [
        [8, 5],
        'A?s*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        -1,
    ],
    [
        5,
        'A?s*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        0,
    ],
    'wildcard with type 1' => [
        ['#N/A', 2], // LibreOffice matches wildcard but shouldn't
        'A*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        1,
    ],
    'wildcard match 3 with type -1' => [
        [8, 3], // LibreOffice matches wildcard but shouldn't
        '*verse',
        ['Obtuse', 'Amuse', 'Obverse', 'Inverse', 'Assurance', 'Amplitude', 'Adverse', 'Apartment'],
        -1,
    ],
    [
        3,
        '*verse',
        ['Obtuse', 'Amuse', 'Obverse', 'Inverse', 'Assurance', 'Amplitude', 'Adverse', 'Apartment'],
        0,
    ],
    [
        3, // Expected
        '*~~*', // contains a tilde
        ['aAAAAA', 'a123456*c', 'abc~xyz', 'alembic'],
        0,
    ],
    [
        2, // Expected
        'abc/123*', // wildcard search contains a forward slash
        ['abc123fff', 'abc/123fff'],
        0,
    ],
    'float lookup int array type0' => [
        1, // Expected
        2.0, // Input
        [2, 3, 4, 5],
        0,
    ],
    'int lookup float array type0' => [
        2, // Expected
        3, // Input
        [2, 3.0, 4, 5],
        0,
    ],
    'int lookup float array type0 not equal' => [
        '#N/A', // Expected
        3, // Input
        [2, 3.1, 4, 5],
        0,
    ],
    'float lookup int array type0 not equal' => [
        '#N/A', // Expected
        3.1, // Input
        [2, 3, 4, 5],
        0,
    ],
    'float lookup int array type1 equal' => [
        1, // Expected
        2.0, // Input
        [2, 3, 4, 5],
        1,
    ],
    'int lookup float array type1 equal' => [
        2, // Expected
        3, // Input
        [2, 3.0, 4, 5],
        1,
    ],
    'float lookup int array type1 less' => [
        1, // Expected
        2.5, // Input
        [2, 3, 4, 5],
        1,
    ],
    'int lookup float array type1 less' => [
        2, // Expected
        3, // Input
        [2, 2.9, 4, 5],
        1,
    ],
    'float lookup int array type -1 equal' => [
        4, // Expected
        2.0, // Input
        [5, 4, 3, 2],
        -1,
    ],
    'int lookup float array type -1 equal' => [
        3, // Expected
        3, // Input
        [5, 4, 3.0, 2],
        -1,
    ],
    'float lookup int array type -1 greater' => [
        2, // Expected
        3.5, // Input
        [5, 4, 3, 2],
        -1,
    ],
    'int lookup float array type -1 greater' => [
        2, // Expected
        3, // Input
        [5, 4, 2.9, 2],
        -1,
    ],
    'default type is type1' => [
        3, // Expected
        4, // Input
        [1, 2, 3, 5],
    ],
    'same as previous but explicit type1' => [
        3, // Expected
        4, // Input
        [1, 2, 3, 5],
    ],
    'same as previous but type0 so different result' => [
        '#N/A', // Expected
        4, // Input
        [1, 2, 3, 5],
        0,
    ],
    'undefined behavior N/A in Excel 4 in PhpSpreadsheet' => [
        'incomplete', // Expected
        4, // Input
        [8, 6, 3, 1],
    ],
    'same as previous but type -1 so match' => [
        2, // Expected
        4, // Input
        [8, 6, 3, 1],
        -1,
    ],
];
