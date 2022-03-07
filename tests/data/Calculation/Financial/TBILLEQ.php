<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [
        0.094151494,
        '31-Mar-2008', '1-Jun-2008', 0.0914,
    ],
    [
        ExcelError::VALUE(),
        'Not a Valid Date', '1-Jun-2008', 0.09,
    ],
    [
        ExcelError::VALUE(),
        '31-Mar-2008', 'Not a Valid Date', 0.09,
    ],
    [
        ExcelError::VALUE(),
        '31-Mar-2008', '1-Jun-2008', 'NaN',
    ],
    [
        ExcelError::NAN(),
        '31-Mar-2008', '1-Jun-2008', -0.09,
    ],
    [
        ExcelError::NAN(),
        '31-Mar-2000', '1-Jun-2021', 0.09,
    ],
    [
        ExcelError::NAN(),
        '1-Jun-2008', '31-Mar-2008', 0.09,
    ],
    [
        0.025465926,
        '5-Feb-2019', '1-Feb-2020', 0.0245,
    ],
    [
        0.036787997,
        '1-Feb-2016', '30-Jan-2017', 0.035,
    ],
    [
        0.025612238,
        '1-Feb-2017', '30-Jun-2017', 0.025,
    ],
];
