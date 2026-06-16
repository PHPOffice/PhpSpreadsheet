<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

abstract class LookupBase
{
    protected static function validateLookupArray(mixed $lookupArray): void
    {
        if (!is_array($lookupArray)) {
            throw new Exception(ExcelError::REF());
        }
    }

    /**
     * @param mixed[] $lookupArray
     * @param float|int|string $index_number number >= 1
     */
    protected static function validateIndexLookup(array $lookupArray, $index_number): int
    {
        // index_number must be a number greater than or equal to 1.
        // Excel results are inconsistent when index is non-numeric.
        // VLOOKUP(whatever, whatever, SQRT(-1)) yields NUM error, but
        // VLOOKUP(whatever, whatever, cellref) yields REF error
        //   when cellref is '=SQRT(-1)'. So just try our best here.
        // Similar results if string (literal yields VALUE, cellRef REF).
        if (!is_numeric($index_number)) {
            throw new Exception(ExcelError::throwError($index_number));
        }
        if ($index_number < 1) {
            throw new Exception(ExcelError::VALUE());
        }

        // index_number must be less than or equal to the number of columns in lookupArray
        if (empty($lookupArray)) {
            throw new Exception(ExcelError::REF());
        }

        return (int) $index_number;
    }

    protected static function checkMatch(
        bool $bothNumeric,
        bool $bothNotNumeric,
        bool $notExactMatch,
        int $rowKey,
        string $cellDataLower,
        string $lookupLower,
        ?int $rowNumber
    ): ?int {
        // remember the last key, but only if datatypes match
        if ($bothNumeric || $bothNotNumeric) {
            // Spreadsheets software returns first exact match,
            // we have sorted and we might have broken key orders
            // we want the first one (by its initial index)
            if ($notExactMatch) {
                $rowNumber = $rowKey;
            } elseif (($cellDataLower == $lookupLower) && (($rowNumber === null) || ($rowKey < $rowNumber))) {
                $rowNumber = $rowKey;
            }
        }

        return $rowNumber;
    }
}
