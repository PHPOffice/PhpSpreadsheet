<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Search
{
    /**
     * SEARCHSENSITIVE.
     *
     * @param mixed (string) $needle The string to look for
     * @param mixed (string) $haystack The string in which to look
     * @param mixed (int) $offset Offset within $haystack
     *
     * @return int|string
     */
    public static function sensitive($needle, $haystack, $offset = 1)
    {
        $needle = Functions::flattenSingleValue($needle);
        $haystack = Functions::flattenSingleValue($haystack);
        $offset = Functions::flattenSingleValue($offset);

        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = ($haystack) ? Calculation::getTRUE() : Calculation::getFALSE();
            }

            if (($offset > 0) && (StringHelper::countCharacters($haystack) > $offset)) {
                if (StringHelper::countCharacters($needle) === 0) {
                    return $offset;
                }

                $pos = mb_strpos($haystack, $needle, --$offset, 'UTF-8');
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }

        return Functions::VALUE();
    }

    /**
     * SEARCHINSENSITIVE.
     *
     * @param mixed (string) $needle The string to look for
     * @param mixed (string) $haystack The string in which to look
     * @param mixed (int) $offset Offset within $haystack
     *
     * @return int|string
     */
    public static function insensitive($needle, $haystack, $offset = 1)
    {
        $needle = Functions::flattenSingleValue($needle);
        $haystack = Functions::flattenSingleValue($haystack);
        $offset = Functions::flattenSingleValue($offset);

        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = ($haystack) ? Calculation::getTRUE() : Calculation::getFALSE();
            }

            if (($offset > 0) && (StringHelper::countCharacters($haystack) > $offset)) {
                if (StringHelper::countCharacters($needle) === 0) {
                    return $offset;
                }

                $pos = mb_stripos($haystack, $needle, --$offset, 'UTF-8');
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }

        return Functions::VALUE();
    }
}
