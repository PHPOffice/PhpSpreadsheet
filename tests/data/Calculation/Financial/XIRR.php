<?php

// result, message, values, dates, guess

return [
    [
        '#NUM!',
        'If values and dates contain a different number of values, returns the #NUM! error value',
        [4000, -46000],
        ['01/04/2015'],
        0.1
    ],
    [
        '#NUM!',
        'Expects at least one positive cash flow and one negative cash flow; otherwise returns the #NUM! error value',
        [-4000, -46000],
        ['01/04/2015', '2019-06-27'],
        0.1
    ],
    [
        '#NUM!',
        'Expects at least one positive cash flow and one negative cash flow; otherwise returns the #NUM! error value',
        [4000, 46000],
        ['01/04/2015', '2019-06-27'],
        0.1
    ],
    [
        '#VALUE!',
        'If any number in dates is not a valid date, returns the #VALUE! error value',
        [4000, -46000],
        ['01/04/2015', '2019X06-27'],
        0.1
    ],
    [
        '#NUM!',
        'If any number in dates precedes the starting date, XIRR returns the #NUM! error value',
        [1893.67, 139947.43, 52573.25, 48849.74, 26369.16, -273029.18],
        ['2019-06-27', '2019-06-20', '2019-06-21', '2019-06-24', '2019-06-27', '2019-07-27'],
        0.1
    ],
    [
        0.137963527441025,
        'XIRR calculation #1 is incorrect',
        [139947.43, 1893.67, 52573.25, 48849.74, 26369.16, -273029.18],
        ['2019-06-20', '2019-06-27', '2019-06-21', '2019-06-24', '2019-06-27', '2019-07-27'],
        0.1
    ],
    [
        0.09999999,
        'XIRR calculation #2 is incorrect',
        [100.0, -110.0],
        ['2019-06-12', '2020-06-11'],
        0.1
    ],
    [
        '#NUM!',
        'Can\'t find a result that works after FINANCIAL_MAX_ITERATIONS tries, the #NUM! error value is returned',
        [139947.43, 1893.67, 52573.25, 48849.74, 26369.16, -273029.18],
        ['2019-06-20', '2019-06-27', '2019-06-21', '2019-06-24', '2019-06-27', '2019-07-27'],
        0.00000
    ],
];
