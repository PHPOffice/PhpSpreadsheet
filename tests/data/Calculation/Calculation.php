<?php

declare(strict_types=1);

function calculationTestDataGenerator(): array
{
    $dataArray1 = [
        ['please +', 'please *', 'increment'],
        [1, 1, 1], // sum is 3
        [3, 3, 3], // product is 27
    ];
    $set0 = [3, $dataArray1, '=IF(A1="please +", SUM(A2:C2), 2)', 'E5'];

    $set1 = [3, $dataArray1, '=IF(TRUE(), SUM(A2:C2), 2)', 'E5'];

    $formula1 = '=IF(A1="please +",SUM(A2:C2),7 + IF(B1="please *", 4, 2))';
    $set2 = [3, $dataArray1, $formula1, 'E5'];

    $dataArray1[0][0] = 'not please + something else';
    $set3 = [11, $dataArray1, $formula1, 'E5'];

    $dataArray2 = [
        ['flag1', 'flag2', 'flag3', 'flag1'],
        [1, 2, 3, 4],
        [5, 6, 7, 8],
    ];
    $set4 = [3, $dataArray2, '=IF($A$1=$B$1,A2,IF($A$1=$C$1,B2,IF($A$1=$D$1,C2,C3)))', 'E5'];

    $dataArray2[0][0] = 'flag3';
    $set5 = [2, $dataArray2, '=IF(A1=B1,A2,IF(A1=C1,B2,IF(A1=D1,C2,C3)))', 'E5'];

    $dataArray3 = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
    ];
    $set6 = [0, $dataArray3, '=IF(A1+B1>3,C1,0)', 'E5'];

    $dataArray4 = [
        ['noflag',    0, 0],
        [127000,    0, 0],
        [10000,  0.03, 0],
        [20000,  0.06, 0],
        [40000,  0.09, 0],
        [70000,  0.12, 0],
        [90000,  0.03, 0],
    ];
    $formula2 = '=IF(A1="flag",IF(A2<10, 0) + IF(A3<10000, 0))';
    $set7 = [false, $dataArray4, $formula2, 'E5'];

    $dataArray5 = [
        [1, 2],
        [3, 4],
        ['=A1+A2', '=SUM(B1:B2)'],
        ['take A', 0],
    ];
    $formula3 = '=IF(A4="take A", A3, B3)';
    $set8 = [4, $dataArray5, $formula3, 'E5', ['A3'], ['B3']];

    $dataArray6 = [
        ['=IF(22,"a","b")'],
    ];
    $set9 = ['a', $dataArray6, '=A1', 'A2'];

    return [
        $set0, $set1, $set2, $set3, $set4, $set5, $set6, $set7, $set8, $set9,
    ];
}

return calculationTestDataGenerator();
