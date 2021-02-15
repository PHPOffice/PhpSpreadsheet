<?php

return [
    ['178', '10110010'],
    ['100', '1100100'],
    ['#NUM!', '111001010101'], // Too large
    ['5', '101'],
    ['2', '10'],
    ['0', '0'],
    ['#NUM!', '21'], // Invalid binary number
    ['#VALUE!', 'true'], // Boolean okay for ODS, not for Excel/Gnumeric
    ['#VALUE!', 'false'], // Boolean okay for ODS, not for Excel/Gnumeric
    ['-107', '1110010101'], // 2's Complement
    ['-1', '1111111111'], // 2's Complement
    [5, 'A2'],
    ['#NUM!', '"A2"'],
    [0, 'A3'],
    ['exception', ''],
];
