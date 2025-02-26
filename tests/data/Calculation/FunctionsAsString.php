<?php

declare(strict_types=1);

return [
    // Note that these are meant to test the parser, not a comprehensive
    // test of all Excel functions, which tests are handled elsewhere.
    [6, '=SUM(1,2,3)'],
    [60, '=SUM(A1:A3)'],
    [70, '=SUM(A1,A2,A4)'],
    [41, '=SUM(1,namedCell)'],
    [50, '=A1+namedCell'],
    [3, '=MATCH(6,{4,5,6,2},0)'],
    ['#N/A', '=MATCH(8,{4,5,6,2},0)'],
    [7, '=HLOOKUP(5,{1,5,10;2,6,11;3,7,12;4,8,13},3,FALSE)'],
    ['Hello, World.', '=CONCATENATE("Hello, ", "World.")'],
    ['{NON-EMPTY SET}', '=UPPER("{non-EMPTY set}")'], // braces not used for matrix
    ['upper', '=LOWER(B1)'],
    [false, '=and(B2,B3)'],
    [0, '=acos(1)'],
    [0.785398, '=round(atan({1,2,3}),6)'], // {1,2,3} will be flattened to 1
    [2, '=minverse({-2.5,1.5;2,-1})'], // 2 is the flattened result of {2,3;4,5}
    [4, '=MDETERM(MMULT({1,2;3,4},{5,6;7,8}))'], // multiple matrices
    [110, '=SUM(A1,Sheet2!$A$1)'], // different sheet absolute address
    [220, '=SUM(A2,A2B)'], // defined name on different sheet
];
