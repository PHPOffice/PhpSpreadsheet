<?php

return [
    ['427', '"01AB"'],
    ['43981', '"ABCD"'],
    ['246', '"F6"'],
    ['74565', '12345'],
    ['4886718345', '123456789'],
    ['#NUM!', '123.45'],
    ['0', '0'],
    ['#NUM!', '"G3579A"'],
    ['#VALUE!', 'true'],
    ['#VALUE!', 'false'],
    ['#NUM!', '-107'],
    ['165', '"A5"'],
    ['1034160313', '"3DA408B9"'],
    ['-165', '"FFFFFFFF5B"'], // 2's Complement
    ['-1', '"FFFFFFFFFF"'], // 2's Complement
    ['#NUM!', '"1FFFFFFFFFF"'], // Too large
    [11, 'A2'],
    [0, 'A3'],
    [549755813887, '"7fffffffff"'], // highest positive, succeeds even for 32-bit
    [-549755813888, '"8000000000"'], // lowest negative, succeeds even for 32-bit
    [-2147483648, '"ff80000000"'],
    [2147483648, '"80000000"'],
    [2147483647, '"7fffffff"'],
];
