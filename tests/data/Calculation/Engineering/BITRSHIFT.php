<?php

return [
    [5, '20, 2'],
    [3, '52, "4"'],
    ['#VALUE!', '"ABC", 5'],
    ['#VALUE!', '5, "ABC"'],
    ['#NUM!', '1, -48'], // result too large
    ['#NUM!', '1.1, 2'], // first arg must be integer
    [1, '4, 2.1'], // second arg will be truncated
    ['#NUM!', '0, 54'], // second arg too large
    [0, '0, 5'],
    ['#NUM!', '-16, 2'], // first arg can't be negative
    [4, '1, -2'], // negative shift permitted
    [4, '1, -2.1'], // negative shift and (ignored) fraction permitted
    [4, '4, Q15'],
    [4, '4, null'],
    [4, '4, false'],
    [2, '4, true'],
    ['exception', ''],
    ['exception', '2'],
    [0, ', 4'],
    [0, 'Q15, 4'],
    [4, '4, q15'],
    [4, '4, false'],
    [2, '4, true'],
    [0, 'false, 4'],
    [0, 'true, 4'],
    [16, 'true, -4'],
    [4, 'A2, 1'],
    [8000000000, '1000000000, -3'], // result > 2**32
    [8000000000, '16000000000, 1'], // argument > 2**32
    ['#NUM!', 'power(2,50), 1'], // argument >= 2**48
    ['1', 'power(2, 47), 47'],
];
