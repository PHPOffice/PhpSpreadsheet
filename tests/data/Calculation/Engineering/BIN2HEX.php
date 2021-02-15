<?php

return [
    ['B2', '10110010'],
    ['#NUM!', '111001010101'], // Too large
    ['00FB', '11111011, 4'], // Leading places
    ['0FB', '11111011, 3.75'], // Leading places as a float
    ['#NUM!', '11111011, -1'], // Leading places negative
    ['#VALUE!', '11111011, "ABC"'], // Leading places non-numeric
    ['E', '"1110"'],
    ['5', '101'],
    ['2', '10'],
    ['0', '0'],
    ['#NUM!', '21'], // Invalid binary number
    ['#VALUE!', 'true'], // ODS accepts Boolean, Excel/Gnumeric don't
    ['#VALUE!', 'false'], // ODS accepts Boolean, Excel/Gnumeric don't
    ['FFFFFFFF95', '1110010101'], // 2's Complement
    ['FFFFFFFFFF', '1111111111'], // 2's Complement
    ['0003', '11, 4'],
    ['#NUM!', '11, 0'],
    ['#NUM!', '11, -1'],
    ['#NUM!', '11, 14'],
    ['#NUM!', '10001, 1'],
    ['11', '10001, 2'],
    [5, 'A2'],
    ['#NUM!', '"A2"'],
    [0, 'A3'],
    ['exception', ''],
];
