<?php

return [
    ['#REF!', null],
    ['#REF!', 'InvalidCellAddress'],
    ['#REF!', 'A2:InvalidCellAddress'],
    [100, 'A1'],
    [500, 'A2:A3'],
    [600, '$A$1:$A$3'],
    [900, 'A2:A4', true],
    [200, 'R2C1', false],
    [200, 'R2C1', 0],
    ['#VALUE!', 'R2C1', '0'],
    [600, 'R1C1:R3C1', false],
    [10, 'OtherSheet!A1'],
    [30, 'OtherSheet!A1:A2'],
    [30, 'OtherSheet!$A$3'],
    [40, 'OtherSheet!R4C1', false],
    [90, 'OtherSheet!R4C1:R5C1', false],
    [90, 'newnr'],
    [90, 'newnr', false],
];
