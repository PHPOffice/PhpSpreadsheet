<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class CharacterConvert
{
    /**
     * CHAR.
     *
     * @param mixed $character Integer Value to convert to its character representation
     */
    public static function character($character): string
    {
        $character = Helpers::validateInt($character);
        $min = Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE ? 0 : 1;
        if ($character < $min || $character > 255) {
            return Functions::VALUE();
        }
        $result = iconv('UCS-4LE', 'UTF-8', pack('V', $character));

        return ($result === false) ? '' : $result;
    }

    /**
     * CODE.
     *
     * @param mixed $characters String character to convert to its ASCII value
     *
     * @return int|string A string if arguments are invalid
     */
    public static function code($characters)
    {
        $characters = Helpers::extractString($characters);
        if ($characters === '') {
            return Functions::VALUE();
        }

        $character = $characters;
        if (mb_strlen($characters, 'UTF-8') > 1) {
            $character = mb_substr($characters, 0, 1, 'UTF-8');
        }

        return self::unicodeToOrd($character);
    }

    private static function unicodeToOrd(string $character): int
    {
        $retVal = 0;
        $iconv = iconv('UTF-8', 'UCS-4LE', $character);
        if ($iconv !== false) {
            $result = unpack('V', $iconv);
            if (is_array($result) && isset($result[1])) {
                $retVal = $result[1];
            }
        }

        return $retVal;
    }
}
