<?php

// result, message, rate, values, dates

return [
    [
        '#VALUE!',
        'If rate is not numeric, returns the #VALUE! error value',
        'xyz',
        [0, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000],
        ['2018-06-30', '2018-12-31', '2019-12-31', '2020-12-31', '2021-12-31', '2022-12-31', '2023-12-31', '2024-12-31', '2025-12-31', '2026-12-31', '2027-12-31'],
    ],
    [
        1000.0,
        'Okay to specify values and dates as non-array',
        0.10,
        1000.0,
        '2018-06-30',
    ],
    [
        '#NUM!',
        'If different number of elements in values and dates, return NUM',
        0.10,
        [1000.0, 1000.1],
        '2018-06-30',
    ],
    [
        '#NUM!',
        'If minimum value > 0, return NUM',
        0.10,
        [1000.0, 1000.1],
        ['2018-06-30', '2018-07-30'],
    ],
    [
        '#NUM!',
        'If maximum value < 0, return NUM',
        0.10,
        [-1000.0, -1000.1],
        ['2018-06-30', '2018-07-30'],
    ],
    [
        '#VALUE!',
        'If any value is non-numeric, return VALUE',
        0.10,
        [-1000.0, 1000.1, 'x'],
        ['2018-06-30', '2018-07-30', '2018-08-30'],
    ],
    [
        '#VALUE!',
        'If first date is non-numeric, return VALUE',
        0.10,
        [-1000.0, 1000.1, 1000.2],
        ['2018-06x30', '2018-07-30', '2018-08-30'],
    ],
    [
        '#VALUE!',
        'If any other date is non-numeric, return VALUE',
        0.10,
        [-1000.0, 1000.1, 1000.2],
        ['2018-06-30', '2018-07-30', '2018-08z30'],
    ],
    [
        '#NUM!',
        'If any date is before first date, return NUM',
        0.10,
        [-1000.0, 1000.1, 1000.2],
        ['2018-06-30', '2018-07-30', '2018-05-30'],
    ],
    [
        772830.734,
        'XNPV calculation #1 is incorrect',
        0.10,
        [0, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000],
        ['2018-06-30', '2018-12-31', '2019-12-31', '2020-12-31', '2021-12-31', '2022-12-31', '2023-12-31', '2024-12-31', '2025-12-31', '2026-12-31', '2027-12-31'],
    ],
    [
        22.257507852701,
        'Gnumeric gets this right, Excel returns #NUM, Libre returns incorrect result',
        -0.10,
        [-100.0, 110.0],
        ['2019-12-31', '2020-12-31'],
    ],
];
