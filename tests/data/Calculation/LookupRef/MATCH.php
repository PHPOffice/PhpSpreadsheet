<?php

return [
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
        2, // Expected
        2, // Input
        [2, 0, 4, 3],
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
    [
        3,
        'x',
        [[0], [0], ['x'], ['x'], ['x']],
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
    [
        '#N/A', // Expected
        6,
        ['6'],
        -1,
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
    [
        '#N/A', // Expected (Invalid match type)
        'abc',
        [1, 22, 'aaa'],
        123,
    ],
    [
        '#N/A', // Expected (Empty lookup array)
        'abc',
        [],
        1,
    ],
    [
        8,
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
    [
        '#N/A',
        'A*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        1,
    ],
    [
        8,
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
    [
        '#N/A',
        'A*e',
        ['Aardvark', 'Apple', 'Armadillo', 'Acre', 'Absolve', 'Amplitude', 'Adverse', 'Apartment'],
        1,
    ],
    [
        8,
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
];
