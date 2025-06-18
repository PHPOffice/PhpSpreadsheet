<?php

// Settlement, Maturity, Frequency, Basis, Result

return [
    [
        4,
        '25-Jan-2007',
        '15-Nov-2008',
        2,
        1,
    ],
    [
        8,
        '2011-01-01',
        '2012-10-25',
        4,
        0,
    ],
    [
        '#VALUE!',
        'Invalid Date',
        '15-Nov-2008',
        2,
        1,
    ],
    [
        '#VALUE!',
        '25-Jan-2007',
        'Invalid Date',
        2,
        1,
    ],
    [
        '#NUM!',
        '25-Jan-2007',
        '15-Nov-2008',
        3,
        1,
    ],
    [
        5,
        '01-Jan-2008',
        '31-Dec-2012',
        1,
        1,
    ],
];
