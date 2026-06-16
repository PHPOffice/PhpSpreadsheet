<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Standardize extends StatisticalValidations
{
    use ArrayEnabled;

    /**
     * STANDARDIZE.
     *
     * Returns a normalized value from a distribution characterized by mean and standard_dev.
     *
     * @param array<mixed>|float $value Value to normalize
     *                      Or can be an array of values
     * @param array<mixed>|float $mean Mean Value
     *                      Or can be an array of values
     * @param array<mixed>|float $stdDev Standard Deviation
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string Standardized value, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function execute($value, $mean, $stdDev): array|string|float
    {
        if (is_array($value) || is_array($mean) || is_array($stdDev)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $mean, $stdDev);
        }

        try {
            $value = self::validateFloat($value);
            $mean = self::validateFloat($mean);
            $stdDev = self::validateFloat($stdDev);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($stdDev <= 0) {
            return ExcelError::NAN();
        }

        return ($value - $mean) / $stdDev;
    }
}
