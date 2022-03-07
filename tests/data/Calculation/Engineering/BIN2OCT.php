<?php

return [
    ['144', 1100100],
    ['262', '10110010'],
    ['#NUM!', '111001010101'], // Too large
    ['011', '1001, 3'], // Leading places
    ['0011', '1001, 4.75'], // Leading places as a float
    ['#NUM!', '1001, -1'], // Leading places negative
    ['#VALUE!', '1001, "ABC"'], // Leading places non-numeric
    ['2', '00000010'],
    ['5', '00000101'],
    ['15', '00001101'],
    ['0', '0'],
    ['#NUM!', '21'], // Invalid binary number
    ['#VALUE!', 'true'], // Boolean okay for ODS, not for others
    ['#VALUE!', 'false'], // Boolean okay for ODS, not for others
    ['7777777625', '1110010101'], // 2's Complement
    ['7777777777', '1111111111'], // 2's Complement
    ['7777777000', '1000000000'], // lowest negative
    ['777', '111111111'], // highest positive
    ['0', '0000000000'],
    ['1', '000000001'],
    ['400', '0100000000'],
    ['400', '100000000'],
    ['7777777400', '1100000000'],
    ['0003', '11, 4'],
    ['#NUM!', '11, 0'],
    ['#NUM!', '11, -1'],
    ['#NUM!', '11, 14'],
    ['#NUM!', '10001, 1'],
    ['21', '10001, 2'],
    [5, 'A2'],
    ['#NUM!', '"A2"'],
    [0, 'A3'],
    ['exception', ''],
];
