<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

abstract class LookupBase
{
    /**
     * @param mixed $lookup_array
     */
    protected static function validateLookupArray($lookup_array): void
    {
        if (!is_array($lookup_array)) {
            throw new Exception(ExcelError::REF());
        }
    }

    protected static function validateIndexLookup(array $lookup_array, $index_number): int
    {
        // index_number must be a number greater than or equal to 1
        if (!is_numeric($index_number) || $index_number < 1) {
            throw new Exception(ExcelError::VALUE());
        }

        // index_number must be less than or equal to the number of columns in lookup_array
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
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
