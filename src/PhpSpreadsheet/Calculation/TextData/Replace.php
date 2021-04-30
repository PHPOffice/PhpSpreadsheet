<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

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
        $oldText = Functions::flattenSingleValue($oldText);
        $start = Functions::flattenSingleValue($start);
        $chars = Functions::flattenSingleValue($chars);
        $newText = Functions::flattenSingleValue($newText);

        $left = Extract::left($oldText, $start - 1);
        $right = Extract::right($oldText, Text::length($oldText) - ($start + $chars) + 1);

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
    public static function substitute($text = '', $fromText = '', $toText = '', $instance = 0): string
    {
        $text = Functions::flattenSingleValue($text);
        $fromText = Functions::flattenSingleValue($fromText);
        $toText = Functions::flattenSingleValue($toText);
        $instance = floor(Functions::flattenSingleValue($instance));

        if ($instance == 0) {
            return str_replace($fromText, $toText, $text);
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
