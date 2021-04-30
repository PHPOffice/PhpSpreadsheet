<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class CharacterConvert
{
    /**
     * CHARACTER.
     *
     * @param mixed $character Integer Value to convert to its character representation
     */
    public static function character($character): string
    {
        $character = Functions::flattenSingleValue($character);

        if (!is_numeric($character)) {
            return Functions::VALUE();
        }

        $character = (int) $character;
        if ($character < 1 || $character > 255) {
            return Functions::VALUE();
        }

        return iconv('UCS-4LE', 'UTF-8', pack('V', $character));
    }

    /**
     * ASCIICODE.
     *
     * @param mixed $characters String character to convert to its ASCII value
     *
     * @return int|string A string if arguments are invalid
     */
    public static function code($characters)
    {
        if (($characters === null) || ($characters === '')) {
            return Functions::VALUE();
        }
        $characters = Functions::flattenSingleValue($characters);
        if (is_bool($characters)) {
            $characters = self::convertBooleanValue($characters);
        }

        $character = $characters;
        if (mb_strlen($characters, 'UTF-8') > 1) {
            $character = mb_substr($characters, 0, 1, 'UTF-8');
        }

        return self::unicodeToOrd($character);
    }

    private static function unicodeToOrd($character)
    {
        return unpack('V', iconv('UTF-8', 'UCS-4LE', $character))[1];
    }

    private static function convertBooleanValue($value)
    {
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
            return (int) $value;
        }

        return ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
    }
}
