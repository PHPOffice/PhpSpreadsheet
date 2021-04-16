<?php

return [
    [3, 'omitted'],
    ['#NAME?', 'InvalidCellAddress'],
    ['#NAME?', 'A2:InvalidCellAddress'],
    [7, 'C7'],
    [7, '$C$7'],
    [2, 'namedrangex'],
    [2, 'namedrangey'],
    [4, 'namedrange3'],
    ['#NAME?', 'namedrange2'],
    [1, 'OtherSheet!A1'],
    [1, 'UnknownSheet!A1'],
    ['#NAME?', 'localname'],
    //[6, 'OtherSheet!localname'], // Never reaches function
];
