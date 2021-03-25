<?php

use PhpOffice\PhpSpreadsheet\Shared\Date;

return [
    [
        0.6,
        [3, 6, 9, 12],
        [5, 10, 15, 20],
    ],
    [
        1.0,
        [1, 2, 3, 4],
        [10, 11, 12, 13],
    ],
    [
        0.2,
        [2, 4, 6],
        [10, 20, 30],
    ],
    [
        4.628571428571,
        [3, 7, 17, 20, 20, 27],
        [1, 2, 3, 4, 5, 6],
    ],
    [
        9.472222222222,
        [
            Date::stringToExcel('1900-02-01'),
            Date::stringToExcel('1900-03-01'),
            Date::stringToExcel('1900-09-01'),
            Date::stringToExcel('1900-01-01'),
            Date::stringToExcel('1900-08-01'),
            Date::stringToExcel('1900-07-01'),
            Date::stringToExcel('1900-05-01'),
        ],
        [
            6,
            5,
            11,
            7,
            5,
            4,
            4,
        ],
    ],
    [
        0.305555555556,
        [
            Date::stringToExcel('1900-01-02'),
            Date::stringToExcel('1900-01-03'),
            Date::stringToExcel('1900-01-09'),
            Date::stringToExcel('1900-01-01'),
            Date::stringToExcel('1900-01-08'),
            Date::stringToExcel('1900-01-07'),
            Date::stringToExcel('1900-01-05'),
        ],
        [
            6,
            5,
            11,
            7,
            5,
            4,
            4,
        ],
    ],
    [
        '#N/A',
        [1, 2, 3],
        [4, 5],
    ],
    [
        '#DIV/0!',
        [1, 2, 3],
        [4, null, null],
    ],
];
