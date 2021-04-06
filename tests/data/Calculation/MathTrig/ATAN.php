<?php

return [
    ['exception', ''],
    ['#VALUE!', '"ABC"'],
    [0, '0'],
    [M_PI / 4, '"1"'],
    [1.107149, '2'],
    [-M_PI / 4, '-1'],
    [M_PI / 2, '10000000'],
    // ['#NUM!', 2], Believe NAN is not possible
    [0, 'Q15'],
    [0, 'false'],
    [M_PI / 4, 'true'],
    [1.373400767, 'A2'],
];
