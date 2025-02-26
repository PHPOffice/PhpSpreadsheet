<?php

declare(strict_types=1);

// Result, Settlement, Maturity, Rate, Yield, Redemption, Frequency, Basis
// On the result line, the value is ODS's calculation.
//     This agrees with Gnumeric, PhpSpreadsheet, and the published algorithm at:
//https://support.office.com/en-us/article/price-function-3ea9deac-8dfa-436f-a7c8-17ea02c21b0a.
// The commented-out value on the next line is Excel's result.
// I do not know how best to reconcile the different results.
// The problem seems restricted to basis codes 2 and 3.

return [
    [
        94.60241717687768,
        // 94.636564030025099,
        '15-Feb-2008',
        '15-Nov-2017',
        0.0575,
        0.065,
        100,
        2,
        2,
    ],
    [
        94.643594548258,
        // 94.635174796784497,
        '15-Feb-2008',
        '15-Nov-2017',
        0.0575,
        0.065,
        100,
        2,
        3,
    ],
    [
        110.74436592216529,
        // 110.83448359321601,
        '01-Apr-2012',
        '31-Mar-2020',
        0.12,
        0.10,
        100,
        2,
        2,
    ],
    [
        110.81970970927745,
        // 110.83452855143901,
        '01-Apr-2012',
        '31-Mar-2020',
        0.12,
        0.10,
        100,
        2,
        3,
    ],
    [
        110.8912556,
        // 110.9216934,
        '01-Apr-2012',
        '31-Mar-2020',
        0.12,
        0.10,
        100,
        4,
        2,
    ],
    [
        110.9292394066714,
        // 110.921732963198,
        '01-Apr-2012',
        '31-Mar-2020',
        0.12,
        0.10,
        100,
        4,
        3,
    ],
];
