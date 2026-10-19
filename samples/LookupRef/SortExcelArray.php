<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
require_once __DIR__ . '/../templates/SortExcel.php';
$helper->log('Emulating how Excel sorts different DataTypes');

/** @param mixed[] $original */
function displaySorted(array $original, Sample $helper, bool $libreSemantics = false): void
{
    $sorted = $original;
    $sortExcel = new SortExcel();
    $sortExcel->sortArray($sorted, libreSemantics: $libreSemantics);
    $outArray = [['Original', 'Sorted']];
    $count = count($original);
    for ($i = 0; $i < $count; ++$i) {
        $outArray[] = [$original[$i], $sorted[$i]];
    }
    $helper->displayGrid($outArray, TextGridRightAlign::floatOrInt);
}

$helper->log('First example');
$original = ['-3', '40', 'A', 'B', true, false, '+3', '1', '10', '2', '25', 1, 0, -1, '-2.5'];
displaySorted($original, $helper);

$helper->log('First example with LibreOffice semantics');
$original = ['-3', '40', 'A', 'B', true, false, '+3', '1', '10', '2', '25', 1, 0, -1, '-2.5'];
displaySorted($original, $helper, true);

$helper->log('Second example');
$original = ['a', 'A', null, 'x', 'X', true, false, -3, 1];
displaySorted($original, $helper);
