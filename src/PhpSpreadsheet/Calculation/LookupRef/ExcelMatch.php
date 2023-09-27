<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Internal\WildcardMatch;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class ExcelMatch
{
    use ArrayEnabled;

    public const MATCHTYPE_SMALLEST_VALUE = -1;
    public const MATCHTYPE_FIRST_VALUE = 0;
    public const MATCHTYPE_LARGEST_VALUE = 1;

    /**
     * MATCH.
     *
     * The MATCH function searches for a specified item in a range of cells
     *
     * Excel Function:
     *        =MATCH(lookup_value, lookup_array, [match_type])
     *
     * @param mixed $lookupValue The value that you want to match in lookup_array
     * @param mixed $lookupArray The range of cells being searched
     * @param mixed $matchType The number -1, 0, or 1. -1 means above, 0 means exact match, 1 means below.
     *                         If match_type is 1 or -1, the list has to be ordered.
     *
     * @return array|int|string The relative position of the found item
     */
    public static function MATCH(mixed $lookupValue, mixed $lookupArray, mixed $matchType = self::MATCHTYPE_LARGEST_VALUE): array|string|int|float
    {
        if (is_array($lookupValue)) {
            return self::evaluateArrayArgumentsIgnore([self::class, __FUNCTION__], 1, $lookupValue, $lookupArray, $matchType);
        }

        $lookupArray = Functions::flattenArray($lookupArray);

        try {
            // Input validation
            self::validateLookupValue($lookupValue);
            $matchType = self::validateMatchType($matchType);
            self::validateLookupArray($lookupArray);

            $keySet = array_keys($lookupArray);
            if ($matchType == self::MATCHTYPE_LARGEST_VALUE) {
                // If match_type is 1 the list has to be processed from last to first
                $lookupArray = array_reverse($lookupArray);
                $keySet = array_reverse($keySet);
            }

            $lookupArray = self::prepareLookupArray($lookupArray, $matchType);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // MATCH() is not case sensitive, so we convert lookup value to be lower cased if it's a string type.
        if (is_string($lookupValue)) {
            $lookupValue = StringHelper::strToLower($lookupValue);
        }

        $valueKey = match ($matchType) {
            self::MATCHTYPE_LARGEST_VALUE => self::matchLargestValue($lookupArray, $lookupValue, $keySet),
            self::MATCHTYPE_FIRST_VALUE => self::matchFirstValue($lookupArray, $lookupValue),
            default => self::matchSmallestValue($lookupArray, $lookupValue),
        };

        if ($valueKey !== null) {
            return ++$valueKey;
        }

        // Unsuccessful in finding a match, return #N/A error value
        return ExcelError::NA();
    }

    private static function matchFirstValue(array $lookupArray, mixed $lookupValue): int|string|null
    {
        if (is_string($lookupValue)) {
            $valueIsString = true;
            $wildcard = WildcardMatch::wildcard($lookupValue);
        } else {
            $valueIsString = false;
            $wildcard = '';
        }

        $valueIsNumeric = is_int($lookupValue) || is_float($lookupValue);
        foreach ($lookupArray as $i => $lookupArrayValue) {
            if (
                $valueIsString
                && is_string($lookupArrayValue)
            ) {
                if (WildcardMatch::compare($lookupArrayValue, $wildcard)) {
                    return $i; // wildcard match
                }
            } else {
                if ($lookupArrayValue === $lookupValue) {
                    return $i; // exact match
                }
                if (
                    $valueIsNumeric
                    && (is_float($lookupArrayValue) || is_int($lookupArrayValue))
                    && $lookupArrayValue == $lookupValue
                ) {
                    return $i; // exact match
                }
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    private static function matchLargestValue(array $lookupArray, mixed $lookupValue, array $keySet)
    {
        if (is_string($lookupValue)) {
            if (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
                $wildcard = WildcardMatch::wildcard($lookupValue);
                foreach (array_reverse($lookupArray) as $i => $lookupArrayValue) {
                    if (is_string($lookupArrayValue) && WildcardMatch::compare($lookupArrayValue, $wildcard)) {
                        return $i;
                    }
                }
            } else {
                foreach ($lookupArray as $i => $lookupArrayValue) {
                    if ($lookupArrayValue === $lookupValue) {
                        return $keySet[$i];
                    }
                }
            }
        }
        $valueIsNumeric = is_int($lookupValue) || is_float($lookupValue);
        foreach ($lookupArray as $i => $lookupArrayValue) {
            if ($valueIsNumeric && (is_int($lookupArrayValue) || is_float($lookupArrayValue))) {
                if ($lookupArrayValue <= $lookupValue) {
                    return array_search($i, $keySet);
                }
            }
            $typeMatch = gettype($lookupValue) === gettype($lookupArrayValue);
            if ($typeMatch && ($lookupArrayValue <= $lookupValue)) {
                return array_search($i, $keySet);
            }
        }

        return null;
    }

    private static function matchSmallestValue(array $lookupArray, mixed $lookupValue): int|string|null
    {
        $valueKey = null;
        if (is_string($lookupValue)) {
            if (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
                $wildcard = WildcardMatch::wildcard($lookupValue);
                foreach ($lookupArray as $i => $lookupArrayValue) {
                    if (is_string($lookupArrayValue) && WildcardMatch::compare($lookupArrayValue, $wildcard)) {
                        return $i;
                    }
                }
            }
        }

        $valueIsNumeric = is_int($lookupValue) || is_float($lookupValue);
        // The basic algorithm is:
        // Iterate and keep the highest match until the next element is smaller than the searched value.
        // Return immediately if perfect match is found
        foreach ($lookupArray as $i => $lookupArrayValue) {
            $typeMatch = gettype($lookupValue) === gettype($lookupArrayValue);
            $bothNumeric = $valueIsNumeric && (is_int($lookupArrayValue) || is_float($lookupArrayValue));

            if ($lookupArrayValue === $lookupValue) {
                // Another "special" case. If a perfect match is found,
                // the algorithm gives up immediately
                return $i;
            }
            if ($bothNumeric && $lookupValue == $lookupArrayValue) {
                return $i; // exact match, as above
            }
            if (($typeMatch || $bothNumeric) && $lookupArrayValue >= $lookupValue) {
                $valueKey = $i;
            } elseif ($typeMatch && $lookupArrayValue < $lookupValue) {
                //Excel algorithm gives up immediately if the first element is smaller than the searched value
                break;
            }
        }

        return $valueKey;
    }

    private static function validateLookupValue(mixed $lookupValue): void
    {
        // Lookup_value type has to be number, text, or logical values
        if ((!is_numeric($lookupValue)) && (!is_string($lookupValue)) && (!is_bool($lookupValue))) {
            throw new Exception(ExcelError::NA());
        }
    }

    private static function validateMatchType(mixed $matchType): int
    {
        // Match_type is 0, 1 or -1
        // However Excel accepts other numeric values,
        //  including numeric strings and floats.
        //  It seems to just be interested in the sign.
        if (!is_numeric($matchType)) {
            throw new Exception(ExcelError::Value());
        }
        if ($matchType > 0) {
            return self::MATCHTYPE_LARGEST_VALUE;
        }
        if ($matchType < 0) {
            return self::MATCHTYPE_SMALLEST_VALUE;
        }

        return self::MATCHTYPE_FIRST_VALUE;
    }

    private static function validateLookupArray(array $lookupArray): void
    {
        // Lookup_array should not be empty
        $lookupArraySize = count($lookupArray);
        if ($lookupArraySize <= 0) {
            throw new Exception(ExcelError::NA());
        }
    }

    private static function prepareLookupArray(array $lookupArray, mixed $matchType): array
    {
        // Lookup_array should contain only number, text, or logical values, or empty (null) cells
        foreach ($lookupArray as $i => $value) {
            //    check the type of the value
            if ((!is_numeric($value)) && (!is_string($value)) && (!is_bool($value)) && ($value !== null)) {
                throw new Exception(ExcelError::NA());
            }
            // Convert strings to lowercase for case-insensitive testing
            if (is_string($value)) {
                $lookupArray[$i] = StringHelper::strToLower($value);
            }
            if (
                ($value === null)
                && (($matchType == self::MATCHTYPE_LARGEST_VALUE) || ($matchType == self::MATCHTYPE_SMALLEST_VALUE))
            ) {
                unset($lookupArray[$i]);
            }
        }

        return $lookupArray;
    }
}
