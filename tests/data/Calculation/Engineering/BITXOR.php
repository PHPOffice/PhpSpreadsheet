<?php

return [
    [50, '21, 39'],
    [112, '200, "184"'],
    [216, '114.00, 170.00'],
    ['#VALUE!', '"ABC", "DEF"'],
    ['#VALUE!', '"ABC", 1'],
    ['#VALUE!', '1, "DEF"'],
    ['#NUM!', '12.00, 2.82E14'],
    [5123456789, '5123456788, 1'],
    [2583096320, '5123456789, 7123456789'],
    ['#NUM!', '-5123456788, 1'],
    ['#NUM!', 'power(2, 50), 1'], // argument >= 2**48
    ['#NUM!', '1, power(2, 50)'], // argument >= 2**48
    ['#NUM!', '-2, 1'], // negative argument
    ['#NUM!', '2, -1'], // negative argument
    ['#NUM!', '-2, -1'], // negative argument
    ['#NUM!', '3.1, 1'], // non-integer argument
    ['#NUM!', '3, 1.1'], // non-integer argument
    [4, '4, Q15'],
    [4, '4, null'],
    [4, '4, false'],
    [5, '4, true'],
    ['exception', ''],
    ['exception', '2'],
    [4, ', 4'],
    [4, 'Q15, 4'],
    [4, 'false, 4'],
    [5, 'true, 4'],
    [9, 'A2, 1'],
];
