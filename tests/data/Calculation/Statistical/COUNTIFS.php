<?php

return [
    [
        0,
    ],
    [
        2,
        ['Y', 'Y', 'N'],
        '=Y',
    ],
    [
        3,
        ['A', 'B', 'C', 'B', 'B'],
        '=B',
    ],
    [
        3,
        ['C', 'B', 'A', 'B', 'B'],
        '=B',
    ],
    //    [
    //        2,
    //        [1, 2, 3, 'B', null, '', false],
    //        '<=2',
    //    ],
    //    [
    //        2,
    //        [1, 2, 3, 'B', null, '', false],
    //        '<=B',
    //    ],
    [
        4,
        ['Female', 'Female', 'Female', 'Male', 'Male', 'Male', 'Female', 'Female', 'Female', 'Male', 'Male', 'Male'],
        'Female',
        [0.63, 0.78, 0.39, 0.55, 0.71, 0.51, 0.78, 0.81, 0.49, 0.35, 0.69, 0.65],
        '>60%',
    ],
    [
        2,
        ['Maths', 'English', 'Science', 'Maths', 'English', 'Science', 'Maths', 'English', 'Science', 'Maths', 'English', 'Science'],
        'Science',
        [0.63, 0.78, 0.39, 0.55, 0.71, 0.51, 0.78, 0.81, 0.49, 0.35, 0.69, 0.65],
        '<50%',
    ],
];
