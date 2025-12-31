<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use Exception;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;
use Stringable;

class SortExcel
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
$helper->log('Emulating how Excel sorts different DataTypes');

/** @param mixed[] $original */
function displaySorted(array $original, Sample $helper): void
{
    $sorted = $original;
    $sortExcel = new SortExcel();
    $sortExcel->sortArray($sorted);
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
