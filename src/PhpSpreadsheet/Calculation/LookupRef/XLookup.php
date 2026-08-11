<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use UnhandledMatchError;

class XLookup extends LookupBase
{
    //use ArrayEnabled; // not yet supported

    /**
     * XLOOKUP — PHP emulation of Excel's XLOOKUP function.
     *
     * @param mixed $lookupValue Value to search for
     * @param mixed $lookupArray Expect array, Array to search in
     * @param mixed $returnArray Expect array, Array to return from (must match lookupArray size)
     * @param mixed $ifNotFound Value to return if no match found (default: #N/A!)
     * @param mixed $matchMode expect int 0 = exact match (default)
     *                                     -1 = exact or next smaller
     *                                      1 = exact or next larger
     *                                      2 = wildcard match (* ? ~)
     * @param mixed $searchMode expect int 1  = first to last (default)
     *                                    -1  = last to first
     *                                     2  = binary search ascending
     *                                    -2  = binary search descending
     */
    public static function lookup(
        mixed $lookupValue,
        mixed $lookupArray,
        mixed $returnArray,
        mixed $ifNotFound = '#N/A!',
        mixed $matchMode = 0,
        mixed $searchMode = 1
    ): mixed {
        if (is_array($lookupValue)) {
            $lookupValue = Functions::flattenArray($lookupValue);
            if (count($lookupValue) === 1) {
                $lookupValue = reset($lookupValue);
            }
        }
        if (is_array($lookupValue)) {
            $result = [];
            foreach ($lookupValue as $value) {
                $result[] = self::lookup($value, $lookupArray, $returnArray, $ifNotFound, $matchMode, $searchMode);
            }

            return $result;
        }
        if (!is_array($lookupArray)) {
            $lookupArray = [$lookupArray];
        }
        if (!is_array($returnArray)) {
            $returnArray = [$returnArray];
        }
        $lookupArray = Functions::flattenArray($lookupArray);
        if (count($returnArray) === 1) {
            $returnArray = Functions::flattenArray($returnArray);
        } else {
            $oldArray = $returnArray;
            $returnArray = [];
            foreach ($oldArray as $row) {
                $newRow = Functions::flattenArray($row);
                if (count($newRow) === 1) {
                    $newRow = reset($newRow);
                }
                $returnArray[] = $newRow;
            }
        }
        /*if (is_array($lookupValue)) { // not yet supported by ArrayEnabled
            return self::evaluateArrayArgumentsIgnore([self::class, __FUNCTION__], 1, $lookupValue, $lookupArray, $returnArray, $ifNotFound, $matchMode, $searchMode);
        }*/

        try {
            self::validateLookupArray($lookupArray);
            self::validateLookupArray($returnArray);
            $matchMode = LookupRefValidations::validateInt($matchMode);
            $searchMode = LookupRefValidations::validateInt($searchMode);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (!in_array($matchMode, [0, -1, 1, 2], true)) {
            return ExcelError::VALUE();
        }

        if (count($lookupArray) !== count($returnArray)) {
            return ExcelError::VALUE();
        }

        try {
            $index = match ($searchMode) {
                1 => self::searchLinear($lookupValue, $lookupArray, $matchMode, false),
                -1 => self::searchLinear($lookupValue, $lookupArray, $matchMode, true),
                2 => self::searchBinary($lookupValue, $lookupArray, $matchMode, true),
                -2 => self::searchBinary($lookupValue, $lookupArray, $matchMode, false),
            };
        } catch (UnhandledMatchError) {
            return ExcelError::VALUE();
        }

        return ($index === null) ? $ifNotFound : $returnArray[$index];
    }

    // ---------------------------------------------------------------------------
    // Search strategies
    // ---------------------------------------------------------------------------

    /**
     * Linear search (searchMode 1 and -1).
     *
     * @param mixed[] $lookupArray
     */
    private static function searchLinear(
        mixed $lookupValue,
        array $lookupArray,
        int $matchMode,
        bool $reverse
    ): ?int {

        $keys = array_keys($lookupArray);
        if ($reverse) {
            $keys = array_reverse($keys);
        }

        $bestIdx = null;
        $bestVal = null;

        foreach ($keys as $i) {
            /** @var scalar */
            $candidate = $lookupArray[$i];

            if ($matchMode === 2) {
                // Wildcard: convert Excel wildcards to PHP regex
                /** @var scalar $lookupValue */
                if (self::wildcardMatch((string) $lookupValue, (string) $candidate)) {
                    return $i;
                }

                continue;
            }

            $cmp = self::compareValues($candidate, $lookupValue);

            if ($cmp === 0) {
                return $i; // Exact match — return immediately
            }

            if ($matchMode === -1 && $cmp < 0) {
                // Next smaller: track largest value still below lookupValue
                if ($bestVal === null || self::compareValues($candidate, $bestVal) > 0) {
                    $bestVal = $candidate;
                    $bestIdx = $i;
                }
            }

            if ($matchMode === 1 && $cmp > 0) {
                // Next larger: track smallest value still above lookupValue
                if ($bestVal === null || self::compareValues($candidate, $bestVal) < 0) {
                    $bestVal = $candidate;
                    $bestIdx = $i;
                }
            }
        }
        /** @var ?int $bestIdx */

        return $bestIdx;
    }

    /**
     * Binary search (searchMode 2 and -2)
     * Assumes array is sorted ascending (searchMode 2) or descending (searchMode -2).
     *
     * @param mixed[] $lookupArray
     */
    private static function searchBinary(
        mixed $lookupValue,
        array $lookupArray,
        int $matchMode,
        bool $ascending
    ): ?int {

        $values = array_values($lookupArray);
        $keys = array_keys($lookupArray);
        $lo = 0;
        $hi = count($values) - 1;
        $bestIdx = null;

        while ($lo <= $hi) {
            $mid = intdiv($lo + $hi, 2);
            $cmp = self::compareValues($values[$mid], $lookupValue);

            if (!$ascending) {
                $cmp = -$cmp; // Flip for descending
            }

            if ($cmp === 0) {
                return $keys[$mid]; // Exact match
            }

            if ($cmp < 0) {
                if ($matchMode === -1) {
                    $bestIdx = $keys[$mid]; // Candidate for next smaller
                }
                $lo = $mid + 1;
            } else {
                if ($matchMode === 1) {
                    $bestIdx = $keys[$mid]; // Candidate for next larger
                }
                $hi = $mid - 1;
            }
        }
        /** @var int $bestIdx */

        return ($matchMode !== 0) ? $bestIdx : null;
    }

    /**
     * Compare two values with type coercion matching Excel's behaviour:
     * numbers < strings < booleans
     */
    private static function compareValues(mixed $a, mixed $b): int
    {
        // Numeric comparison
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }
        // String comparison (case-insensitive, like Excel)
        if (is_string($a) && is_string($b)) {
            return strcasecmp($a, $b);
        }
        // Bool comparison
        if (is_bool($a) && is_bool($b)) {
            return $a <=> $b;
        }
        // Cross-type: number < string < bool
        $typeOrder = fn ($v) => match (true) {
            is_numeric($v) => 0,
            is_string($v) => 1,
            is_bool($v) => 2,
            default => 3,
        };

        return $typeOrder($a) <=> $typeOrder($b);
    }

    /**
     * Wildcard match (matchMode 2)
     * Supports Excel wildcards: * (any sequence), ? (any single char), ~ (escape).
     */
    private static function wildcardMatch(string $pattern, string $subject): bool
    {
        // Handle ~* and ~? escapes first
        $regex = '';
        $len = strlen($pattern);
        for ($i = 0; $i < $len; ++$i) {
            $ch = $pattern[$i];
            if ($ch === '~' && $i + 1 < $len) {
                $next = $pattern[++$i];
                $regex .= preg_quote($next, '/');
            } elseif ($ch === '*') {
                $regex .= '.*';
            } elseif ($ch === '?') {
                $regex .= '.';
            } else {
                $regex .= preg_quote($ch, '/');
            }
        }

        return (bool) preg_match('/^' . $regex . '$/i', $subject);
    }
}
