<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Search
{
    /**
     * FIND (case sensitive search).
     *
     * @param mixed $needle The string to look for
     * @param mixed $haystack The string in which to look
     * @param mixed $offset Integer offset within $haystack to start searching from
     *
     * @return int|string
     */
    public static function sensitive($needle, $haystack, $offset = 1)
    {
        try {
            $needle = Helpers::extractString($needle);
            $haystack = Helpers::extractString($haystack);
            $offset = Helpers::extractInt($offset, 1, 0, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        if (StringHelper::countCharacters($haystack) >= $offset) {
            if (StringHelper::countCharacters($needle) === 0) {
                return $offset;
            }

            $pos = mb_strpos($haystack, $needle, --$offset, 'UTF-8');
            if ($pos !== false) {
                return ++$pos;
            }
        }

        return Functions::VALUE();
    }

    /**
     * SEARCH (case insensitive search).
     *
     * @param mixed $needle The string to look for
     * @param mixed $haystack The string in which to look
     * @param mixed $offset Integer offset within $haystack to start searching from
     *
     * @return int|string
     */
    public static function insensitive($needle, $haystack, $offset = 1)
    {
        try {
            $needle = Helpers::extractString($needle);
            $haystack = Helpers::extractString($haystack);
            $offset = Helpers::extractInt($offset, 1, 0, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        if (StringHelper::countCharacters($haystack) >= $offset) {
            if (StringHelper::countCharacters($needle) === 0) {
                return $offset;
            }

            $pos = mb_stripos($haystack, $needle, --$offset, 'UTF-8');
            if ($pos !== false) {
                return ++$pos;
            }
        }

        return Functions::VALUE();
    }
}
