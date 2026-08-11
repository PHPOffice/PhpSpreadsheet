<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use Exception;
use Stringable;

class SortExcel
{
    public const ASCENDING = 1;
    public const DESCENDING = -1;

    private int $arrayCol;

    private int $ascending;

    private bool $libreSemantics = false;

    private function cmp(mixed $rowA, mixed $rowB): int
    {
        $a = is_array($rowA) ? $rowA[$this->arrayCol] : $rowA;
        $b = is_array($rowB) ? $rowB[$this->arrayCol] : $rowB;
        if ($this->libreSemantics) {
            if (is_bool($a)) {
                $a = (int) $a;
            }
            if (is_bool($b)) {
                $b = (int) $b;
            }
        }
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
        /** @var string */
        $a2 = $a;
        /** @var string */
        $b2 = $b;
        if ($this->libreSemantics) {
            if (is_numeric($a2) && is_numeric($b2)) {
                if (str_starts_with($a2, '+')) {
                    if (str_starts_with($b2, '+')) {
                    } elseif (str_starts_with($b2, '-')) {
                        return $this->ascending;
                    } else {
                        return -$this->ascending;
                    }
                } elseif (str_starts_with($b2, '+')) {
                    // $a2 can't start with + here
                    if (str_starts_with($a2, '-')) {
                        return -$this->ascending;
                    }

                    return $this->ascending;
                }
            }
        } else {
            if (is_numeric($a2) && str_starts_with($a2, '-')) {
                $a2 = substr($a2, 1);
            }
            if (is_numeric($b2) && str_starts_with($b2, '-')) {
                $b2 = substr($b2, 1);
            }
        }

        // strings sort case-insensitive
        return $this->ascending * strcasecmp($a2, $b2);
    }

    /**
     * @param mixed[] $array
     */
    public function sortArray(array &$array, int $ascending = self::ASCENDING, int $arrayCol = 0, bool $libreSemantics = false): void
    {
        if ($ascending !== 1 && $ascending !== -1) {
            throw new Exception('ascending must be 1 or -1');
        }
        $this->ascending = $ascending;
        $this->arrayCol = $arrayCol;
        $this->libreSemantics = $libreSemantics;
        usort($array, $this->cmp(...));
    }
}
