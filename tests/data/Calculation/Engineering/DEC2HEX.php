<?php

return [
    ['165', '357'],
    ['54D', '1357'],
    ['F6', '246'],
    ['3039', '12345'],
    ['75BCD15', '123456789'],
    ['0064', '100, 4'],
    ['00064', '100, 5.75'], // Leading places as a float
    ['#NUM!', '100, -1'], // Leading places negative
    ['#VALUE!', '100, "ABC"'], // Leading places non-numeric
    ['7B', '123.45'],
    ['0', '0'],
    ['#VALUE!', '"3579A"'], // Invalid decimal
    ['#VALUE!', 'true'], // ODS accepts boolean, Excel/Gnumeric don't
    ['#VALUE!', 'false'],
    ['FFFFFFFFCA', '-54'], // 2's Complement
    ['FFFFFFFF95', '-107'], // 2's Complement
    ['0103', '259, 4'],
    ['#NUM!', '259, 0'],
    ['#NUM!', '259, -1'],
    ['#NUM!', '259, 14'],
    ['#NUM!', '259, 1'],
    ['103', '259, 3'],
    ['11', 'A2'],
    ['0', 'C2'],
    ['exception', ''],
];
