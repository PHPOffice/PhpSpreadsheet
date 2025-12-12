<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;

class SortExcel
{
    private static function cmp(mixed $a, mixed $b): int
    {
        // null sorts highest
        if (!is_scalar($a)) {
            return (is_scalar($b)) ? 1 : 0;
        }
        if (!is_scalar($b)) {
            return -1;
        }
        // int|float sorts lowest
        $numericA = is_int($a) || is_float($a);
        $numericB = is_int($b) || is_float($b);
        if ($numericA && $numericB) {
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        }
        if ($numericA) {
            return -1;
        }
        if ($numericB) {
            return 1;
        }
        // bool sorts higher than string
        if (is_bool($a)) {
            if (!is_bool($b)) {
                return 1;
            }
            if ($a) {
                return $b ? 0 : 1;
            }

            return $b ? -1 : 0;
        }
        if (is_bool($b)) {
            return -1;
        }
        // special handling for numeric strings starting with -
        $a = (string) preg_replace('/^-(\d)+$/', '$1', $a);
        $b = (string) preg_replace('/^-(\d)+$/', '$1', $b);

        // strings sort case-insensitive
        return strcasecmp($a, $b);
    }

    /**
     * Sort a one-dimensional array in the same order as Excel.
     *
     * @param array<int, null|bool|float|int|string> $array
     */
    public static function sortArray(array &$array): void
    {
        usort($array, self::cmp(...));
        $j = count($array);
        while ($j > 0) {
            --$j;
            if ($array[$j] !== null) {
                break;
            }
            $array[$j] = 0;
        }
    }
}

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
$helper->log('Emulating how Excel sorts different DataTypes');

/** @param array<int, null|bool|float|int|string> $original */
function displaySorted(array $original, Sample $helper): void
{
    $sorted = $original;
    SortExcel::sortArray($sorted);
    $outArray = [['Original', 'Sorted']];
    $count = count($original);
    for ($i = 0; $i < $count; ++$i) {
        $outArray[] = [$original[$i], $sorted[$i]];
    }
    $helper->displayGrid($outArray, TextGridRightAlign::floatOrInt);
}

$helper->log('First example');
$original = ['-3', '40', 'A', 'B', true, false, '+3', '1', '10', '2', '25', 1, 0, -1];
displaySorted($original, $helper);

$helper->log('Second example');
$original = ['a', 'A', null, 'x', 'X', true, false, -3, 1];
displaySorted($original, $helper);
