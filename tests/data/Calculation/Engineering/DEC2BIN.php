<?php

return [
    ['101100101', '357'],
    ['#NUM!', '512'], // Too large
    ['#NUM!', '-513'], // Too small
    ['1001', '9, 4'],
    ['00001001', '9, 8'],
    ['001001', '9, 6.75'], // Leading places as a float
    ['#NUM!', '9, -1'], // Leading places negative
    ['#VALUE!', '9, "ABC"'], // Leading places non-numeric
    ['11110110', '"246"'],
    ['#NUM!', '12345'],
    ['#NUM!', '123456789'],
    ['1111011', '123.45'],
    ['0', '0'],
    ['#VALUE!', '"3579A"'], // Invalid decimal
    ['#VALUE!', 'true'], // ODS accepts boolean, Excel/Gnumeric don't
    ['#VALUE!', 'false'], // ODS accepts boolean, Excel/Gnumeric don't
    ['1110011100', '-100'], // 2's Complement
    ['1110010101', '-107'], // 2's Complement
    ['1000000000', '-512'], // lowest negative
    ['111111111', '511'], // highest positive
    ['#NUM!', '-513'],
    ['#NUM!', '512'],
    ['0011', '3, 4'],
    ['#NUM!', '3, 0'],
    ['#NUM!', '3, -1'],
    ['#NUM!', '3, 14'],
    ['#NUM!', '3, 1'],
    ['11', '3, 2'],
    [101, 'A2'],
    ['#VALUE!', '"A2"'],
    [0, 'A3'],
    ['exception', ''],
];
