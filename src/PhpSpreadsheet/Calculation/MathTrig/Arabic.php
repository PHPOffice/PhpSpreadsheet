<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Arabic
{
    use ArrayEnabled;

    private const ROMAN_LOOKUP = [
        'M' => 1000,
        'D' => 500,
        'C' => 100,
        'L' => 50,
        'X' => 10,
        'V' => 5,
        'I' => 1,
    ];

    /**
     * Recursively calculate the arabic value of a roman numeral.
     */
    private static function calculateArabic(array $roman, int &$sum = 0, int $subtract = 0): int
    {
        $numeral = array_shift($roman);
        if (!isset(self::ROMAN_LOOKUP[$numeral])) {
            throw new Exception('Invalid character detected');
        }

        $arabic = self::ROMAN_LOOKUP[$numeral];
        if (count($roman) > 0 && isset(self::ROMAN_LOOKUP[$roman[0]]) && $arabic < self::ROMAN_LOOKUP[$roman[0]]) {
            $subtract += $arabic;
        } else {
            $sum += ($arabic - $subtract);
            $subtract = 0;
        }

        if (count($roman) > 0) {
            self::calculateArabic($roman, $sum, $subtract);
        }

        return $sum;
    }

    /**
     * ARABIC.
     *
     * Converts a Roman numeral to an Arabic numeral.
     *
     * Excel Function:
     *        ARABIC(text)
     *
     * @param mixed $roman Should be a string, or can be an array of strings
     *
     * @return array|int|string the arabic numberal contrived from the roman numeral
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function evaluate(mixed $roman): array|int|string
    {
        if (is_array($roman)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $roman);
        }

        // An empty string should return 0
        $roman = substr(trim(strtoupper((string) $roman)), 0, 255);
        if ($roman === '') {
            return 0;
        }

        // Convert the roman numeral to an arabic number
        $negativeNumber = $roman[0] === '-';
        if ($negativeNumber) {
            $roman = substr($roman, 1);
        }

        try {
            $arabic = self::calculateArabic(str_split($roman));
        } catch (Exception) {
            return ExcelError::VALUE(); // Invalid character detected
        }

        if ($negativeNumber) {
            $arabic *= -1; // The number should be negative
        }

        return $arabic;
    }
}
