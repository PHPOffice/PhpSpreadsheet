<?php

return [
    [5, '21, 39'],
    [64, '200, "84"'],
    [8, '72.00, 184.00'],
    ['#VALUE!', '"ABC", "DEF"'],
    ['#VALUE!', '1, "DEF"'],
    ['#VALUE!', '"ABC", 1'],
    ['#NUM!', '12.00, 2.82E14'],
    [5123456789, '5123456789, 5123456789'],
    [4831908629, '5123456789, 7123456789'],
    [21, '5123456789, 31'],
    ['#NUM!', '-5123456788, 1'],
    ['#NUM!', 'power(2, 50), 1'], // argument >= 2**48
    ['#NUM!', '1, power(2, 50)'], // argument >= 2**48
    ['#NUM!', '-2, 1'], // negative argument
    ['#NUM!', '2, -1'], // negative argument
    ['#NUM!', '-2, -1'], // negative argument
    ['#NUM!', '3.1, 1'], // non-integer argument
    ['#NUM!', '3, 1.1'], // non-integer argument
    [0, '4, Q15'],
    [0, '4, null'],
    [0, '4, false'],
    [1, '3, true'],
    ['exception', ''],
    ['exception', '2'],
    [0, ', 4'],
    [0, 'Q15, 4'],
    [0, 'false, 4'],
    [1, 'true, 5'],
    [8, 'A2, 9'],
];
