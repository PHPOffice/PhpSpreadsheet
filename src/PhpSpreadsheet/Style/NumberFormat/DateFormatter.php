<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class DateFormatter
{
    /**
     * Search/replace values to convert Excel date/time format masks to PHP format masks.
     *
     * @var array
     */
    private static $dateFormatReplacements = [
        // first remove escapes related to non-format characters
        '\\' => '',
        //    12-hour suffix
        'am/pm' => 'A',
        //    4-digit year
        'e' => 'Y',
        'yyyy' => 'Y',
        //    2-digit year
        'yy' => 'y',
        //    first letter of month - no php equivalent
        'mmmmm' => 'M',
        //    full month name
        'mmmm' => 'F',
        //    short month name
        'mmm' => 'M',
        //    mm is minutes if time, but can also be month w/leading zero
        //    so we try to identify times be the inclusion of a : separator in the mask
        //    It isn't perfect, but the best way I know how
        ':mm' => ':i',
        'mm:' => 'i:',
        //    month leading zero
        'mm' => 'm',
        //    month no leading zero
        'm' => 'n',
        //    full day of week name
        'dddd' => 'l',
        //    short day of week name
        'ddd' => 'D',
        //    days leading zero
        'dd' => 'd',
        //    days no leading zero
        'd' => 'j',
        //    seconds
        'ss' => 's',
        //    fractional seconds - no php equivalent
        '.s' => '',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock).
     *
     * @var array
     */
    private static $dateFormatReplacements24 = [
        'hh' => 'H',
        'h' => 'G',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock).
     *
     * @var array
     */
    private static $dateFormatReplacements12 = [
        'hh' => 'h',
        'h' => 'g',
    ];

    public static function format($value, string $format): string
    {
        // strip off first part containing e.g. [$-F800] or [$USD-409]
        // general syntax: [$<Currency string>-<language info>]
        // language info is in hexadecimal
        // strip off chinese part like [DBNum1][$-804]
        $format = preg_replace('/^(\[DBNum\d\])*(\[\$[^\]]*\])/i', '', $format) ?? '';

        // OpenOffice.org uses upper-case number formats, e.g. 'YYYY', convert to lower-case;
        //    but we don't want to change any quoted strings
        /** @var callable */
        $callable = ['self', 'setLowercaseCallback'];
        $format = preg_replace_callback('/(?:^|")([^"]*)(?:$|")/', $callable, $format);

        // Only process the non-quoted blocks for date format characters
        $blocks = explode('"', $format);
        foreach ($blocks as $key => &$block) {
            if ($key % 2 == 0) {
                $block = strtr($block, self::$dateFormatReplacements);
                if (!strpos($block, 'A')) {
                    // 24-hour time format
                    // when [h]:mm format, the [h] should replace to the hours of the value * 24
                    if (false !== strpos($block, '[h]')) {
                        $hours = (int) ($value * 24);
                        $block = str_replace('[h]', $hours, $block);

                        continue;
                    }
                    $block = strtr($block, self::$dateFormatReplacements24);
                } else {
                    // 12-hour time format
                    $block = strtr($block, self::$dateFormatReplacements12);
                }
            }
        }
        $format = implode('"', $blocks);

        // escape any quoted characters so that DateTime format() will render them correctly
        /** @var callable */
        $callback = ['self', 'escapeQuotesCallback'];
        $format = preg_replace_callback('/"(.*)"/U', $callback, $format);

        $dateObj = Date::excelToDateTimeObject($value);
        // If the colon preceding minute had been quoted, as happens in
        // Excel 2003 XML formats, m will not have been changed to i above.
        // Change it now.
        $format = \preg_replace('/\\\\:m/', ':i', $format);

        return $dateObj->format($format);
    }

    private static function setLowercaseCallback($matches): string
    {
        return mb_strtolower($matches[0]);
    }

    private static function escapeQuotesCallback($matches): string
    {
        return '\\' . implode('\\', str_split($matches[1]));
    }
}
