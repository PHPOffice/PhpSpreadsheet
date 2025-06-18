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
];
