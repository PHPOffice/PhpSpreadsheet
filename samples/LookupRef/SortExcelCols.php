<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use Exception;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;
use Stringable;

// this is the same class as in sortExcel
class SortExcelCols
{
    public const ASCENDING = 1;
    public const DESCENDING = -1;

    private int $arrayCol;

    private int $ascending;

    private function cmp(mixed $rowA, mixed $rowB): int
    {
        $a = is_array($rowA) ? $rowA[$this->arrayCol] : $rowA;
        $b = is_array($rowB) ? $rowB[$this->arrayCol] : $rowB;
        if ($a instanceof Stringable) {
            $a = (string) $a;
        }
        if ($b instanceof Stringable) {
            $b = (string) $b;
        }
        if (is_array($a) || is_object($a) || is_resource($a) || is_array($b) || is_object($b) || is_resource($b)) {
            throw new Exception('Invalid datatype');
        }
        // null sorts highest
        if ($a === null) {
            return ($b === null) ? 0 : $this->ascending;
        }
        if ($b === null) {
            return -$this->ascending;
        }
        // int|float sorts lowest
        $numericA = is_int($a) || is_float($a);
        $numericB = is_int($b) || is_float($b);
        if ($numericA && $numericB) {
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -$this->ascending : $this->ascending;
        }
        if ($numericA) {
            return -$this->ascending;
        }
        if ($numericB) {
            return $this->ascending;
        }
        // bool sorts higher than string
        if (is_bool($a)) {
            if (!is_bool($b)) {
                return $this->ascending;
            }
            if ($a) {
                return $b ? 0 : $this->ascending;
            }

            return $b ? -$this->ascending : 0;
        }
        if (is_bool($b)) {
            return -$this->ascending;
        }
        // special handling for numeric strings starting with -
        /** @var string $a */
        $a2 = (string) preg_replace('/^-(\d)+$/', '$1', $a);
        /** @var string $b */
        $b2 = (string) preg_replace('/^-(\d)+$/', '$1', $b);

        // strings sort case-insensitive
        return $this->ascending * strcasecmp($a2, $b2);
    }

    /**
     * @param mixed[] $array
     */
    public function sortArray(array &$array, int $ascending = self::ASCENDING, int $arrayCol = 0): void
    {
        if ($ascending !== 1 && $ascending !== -1) {
            throw new Exception('ascending must be 1 or -1');
        }
        $this->ascending = $ascending;
        $this->arrayCol = $arrayCol;
        usort($array, $this->cmp(...));
    }
}

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
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
    $sortExcelCols = new SortExcelCols();
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
