<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
require_once __DIR__ . '/../templates/SortExcel.php';
$helper->log('Emulating how Excel sorts different DataTypes by Column');

$array = [
    ['a', 'a', 'a'],
    ['a', 'a', 'b'],
    ['a', null, 'c'],
    ['b', 'b', 1],
    ['b', 'c', 2],
    ['b', 'c', true],
    ['c', 1, false],
    ['c', 1, 'a'],
    ['c', 2, 'b'],
    [1, 2, 'c'],
    [1, true, 1],
    [1, true, 2],
    [2, false, true],
    [2, false, false],
    [2, 'a', false],
    [true, 'b', true],
    [true, 'c', 2],
    [true, 1, 1],
    [false, 2, 'a'],
    [false, true, 'b'],
    [false, false, 'c'],
];

/** @param array<int, array<int, mixed>> $original */
function displaySortedCols(array $original, Sample $helper): void
{
    $sorted = $original;
    $sortExcelCols = new SortExcel();
    $helper->log('Sort by least significant column (descending)');
    $sortExcelCols->sortArray($sorted, arrayCol: 2, ascending: -1);
    $helper->log('Sort by middle column (ascending)');
    $sortExcelCols->sortArray($sorted, arrayCol: 1, ascending: 1);
    $helper->log('Sort by most significant column (descending)');
    $sortExcelCols->sortArray($sorted, arrayCol: 0, ascending: -1);
    $outArray = [['Original', '', '', 'Sorted', '', '']];
    $count = count($original);
    /** @var string[][] $sorted */
    for ($i = 0; $i < $count; ++$i) {
        $outArray[] = [
            $original[$i][0],
            $original[$i][1],
            $original[$i][2],
            $sorted[$i][0],
            $sorted[$i][1],
            $sorted[$i][2],
        ];
    }
    $helper->displayGrid($outArray, TextGridRightAlign::floatOrInt);
}

displaySortedCols($array, $helper);
