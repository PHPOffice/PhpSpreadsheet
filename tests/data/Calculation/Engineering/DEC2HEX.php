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
    ['FF80000001', '-2147483647'], // 2's Complement
    ['FF80000000', '-2147483648'], // 2's Complement
    ['7FFFFFFFFF', 549755813887], // highest positive, succeeds even for 32-bit
    ['#NUM!', 549755813888],
    ['8000000000', -549755813888], // lowest negative, succeeds even for 32-bit
    ['A2DE246000', -400000000000],
    ['5D21DBA000', 400000000000],
    ['#NUM!', -549755813889],
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
