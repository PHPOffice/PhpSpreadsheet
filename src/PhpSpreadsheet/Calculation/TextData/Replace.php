<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Replace
{
    /**
     * REPLACE.
     *
     * @param mixed $oldText The text string value to modify
     * @param mixed $start Integer offset for start character of the replacement
     * @param mixed $chars Integer number of characters to replace from the start offset
     * @param mixed $newText String to replace in the defined position
     */
    public static function replace($oldText, $start, $chars, $newText): string
    {
        try {
            $start = Helpers::extractInt($start, 1, 0, true);
            $chars = Helpers::extractInt($chars, 0, 0, true);
            $oldText = Helpers::extractString($oldText);
            $newText = Helpers::extractString($newText);
            $left = mb_substr($oldText, 0, $start - 1, 'UTF-8');

            $right = mb_substr($oldText, $start + $chars - 1, null, 'UTF-8');
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        return $left . $newText . $right;
    }

    /**
     * SUBSTITUTE.
     *
     * @param mixed $text The text string value to modify
     * @param mixed $fromText The string value that we want to replace in $text
     * @param mixed $toText The string value that we want to replace with in $text
     * @param mixed $instance Integer instance Number for the occurrence of frmText to change
     */
    public static function substitute($text = '', $fromText = '', $toText = '', $instance = null): string
    {
        try {
            $text = Helpers::extractString($text);
            $fromText = Helpers::extractString($fromText);
            $toText = Helpers::extractString($toText);
            $instance = Functions::flattenSingleValue($instance);
            if ($instance === null) {
                return str_replace($fromText, $toText, $text);
            }
            if (is_bool($instance)) {
                if ($instance === false || Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE) {
                    return Functions::Value();
                }
                $instance = 1;
            }
            $instance = Helpers::extractInt($instance, 1, 0, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        $pos = -1;
        while ($instance > 0) {
            $pos = mb_strpos($text, $fromText, $pos + 1, 'UTF-8');
            if ($pos === false) {
                break;
            }
            --$instance;
        }

        if ($pos !== false) {
            return self::REPLACE($text, ++$pos, mb_strlen($fromText, 'UTF-8'), $toText);
        }

        return $text;
    }
}
