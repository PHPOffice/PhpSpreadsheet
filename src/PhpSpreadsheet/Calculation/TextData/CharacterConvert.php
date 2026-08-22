<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class CharacterConvert
{
    use ArrayEnabled;

    private static string $oneByteCharacterSet = 'Windows-1252';

    /**
     * CHAR.
     *
     * @param mixed $character Integer Value to convert to its character representation
     *                              Or can be an array of values
     *
     * @return array<mixed>|string The character string
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function character(mixed $character): array|string
    {
        if (is_array($character)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $character);
        }

        return self::characterBoth($character, true);
    }

    /** @return array<mixed>|string */
    public static function characterUnicode(mixed $character): array|string
    {
        if (is_array($character)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $character);
        }

        return self::characterBoth($character, false);
    }

    private static function characterBoth(mixed $character, bool $ansi = true): string
    {
        try {
            $character = Helpers::validateInt($character, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        if ($ansi && $character === 219 && self::$oneByteCharacterSet[0] === 'M') {
            return '€';
        }

        $min = Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE ? 0 : 1;
        if ($character < $min || ($ansi && $character > 255) || $character > 0x10FFFF) {
            return ExcelError::VALUE();
        }
        if ($character > 0x10FFFD) { // last assigned
            return ExcelError::NA();
        }
        if ($ansi) {
            $result = chr($character);

            return (string) iconv(self::$oneByteCharacterSet, 'UTF-8//IGNORE', $result);
        }

        return mb_chr($character, 'UTF-8');
    }

    /**
     * CODE.
     *
     * @param mixed $characters String character to convert to its ASCII value
     *                              Or can be an array of values
     *
     * @return array<mixed>|int|string A string if arguments are invalid
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function code(mixed $characters): array|string|int
    {
        if (is_array($characters)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $characters);
        }
        if (is_bool($characters) && Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
            $characters = $characters ? '1' : '0';
        }

        return self::codeBoth(StringHelper::convertToString($characters, convertBool: true), true);
    }

    /** @return array<mixed>|int|string */
    public static function codeUnicode(mixed $characters): array|string|int
    {
        if (is_array($characters)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $characters);
        }
        if (is_bool($characters) && Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
            $characters = $characters ? '1' : '0';
        }

        return self::codeBoth(StringHelper::convertToString($characters, convertBool: true), false);
    }

    private static function codeBoth(string $characters, bool $ansi = true): int|string
    {
        try {
            $characters = Helpers::extractString($characters, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        if ($characters === '') {
            return ExcelError::VALUE();
        }

        $character = $characters;
        if (mb_strlen($characters, 'UTF-8') > 1) {
            $character = mb_substr($characters, 0, 1, 'UTF-8');
        }
        if ($ansi && $character === '€' && self::$oneByteCharacterSet[0] === 'M') {
            return 219;
        }

        $result = mb_ord($character, 'UTF-8');
        if ($ansi) {
            $result = iconv('UTF-8', self::$oneByteCharacterSet . '//IGNORE', $character);

            return ($result !== '') ? ord("$result") : 63; // question mark
        }

        return $result;
    }

    public static function setWindowsCharacterSet(): void
    {
        self::$oneByteCharacterSet = 'Windows-1252';
    }

    public static function setMacCharacterSet(): void
    {
        self::$oneByteCharacterSet = 'MAC';
    }
}
