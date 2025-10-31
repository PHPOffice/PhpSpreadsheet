<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use Throwable;

class DateFormatter
{
    /**
     * Search/replace values to convert Excel date/time format masks to PHP format masks.
     */
    private const DATE_FORMAT_REPLACEMENTS = [
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
        //    full day of week name
        'dddd' => 'l',
        //    short day of week name
        'ddd' => 'D',
        //    days leading zero
        'dd' => 'd',
        //    days no leading zero
        'd' => 'j',
        //    fractional seconds - no php equivalent
        '.s' => '',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock).
     */
    private const DATE_FORMAT_REPLACEMENTS24 = [
        'hh' => 'H',
        'h' => 'G',
        //    month leading zero
        'mm' => 'm',
        //    month no leading zero
        'm' => 'n',
        //    seconds
        'ss' => 's',
    ];

    /**
     * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock).
     */
    private const DATE_FORMAT_REPLACEMENTS12 = [
        'hh' => 'h',
        'h' => 'g',
        //    month leading zero
        'mm' => 'm',
        //    month no leading zero
        'm' => 'n',
        //    seconds
        'ss' => 's',
    ];

    private const HOURS_IN_DAY = 24;
    private const MINUTES_IN_DAY = 60 * self::HOURS_IN_DAY;
    private const SECONDS_IN_DAY = 60 * self::MINUTES_IN_DAY;
    private const INTERVAL_PRECISION = 10;
    private const INTERVAL_LEADING_ZERO = [
        '[hh]',
        '[mm]',
        '[ss]',
    ];
    private const INTERVAL_ROUND_PRECISION = [
        // hours and minutes truncate
        '[h]' => self::INTERVAL_PRECISION,
        '[hh]' => self::INTERVAL_PRECISION,
        '[m]' => self::INTERVAL_PRECISION,
        '[mm]' => self::INTERVAL_PRECISION,
        // seconds round
        '[s]' => 0,
        '[ss]' => 0,
    ];
    private const INTERVAL_MULTIPLIER = [
        '[h]' => self::HOURS_IN_DAY,
        '[hh]' => self::HOURS_IN_DAY,
        '[m]' => self::MINUTES_IN_DAY,
        '[mm]' => self::MINUTES_IN_DAY,
        '[s]' => self::SECONDS_IN_DAY,
        '[ss]' => self::SECONDS_IN_DAY,
    ];

    /** @param float|int|numeric-string $value */
    private static function tryInterval(bool &$seekingBracket, string &$block, mixed $value, string $format): void
    {
        if ($seekingBracket) {
            if (str_contains($block, $format)) {
                $hours = (string) (int) round(
                    self::INTERVAL_MULTIPLIER[$format] * $value,
                    self::INTERVAL_ROUND_PRECISION[$format]
                );
                if (strlen($hours) === 1 && in_array($format, self::INTERVAL_LEADING_ZERO, true)) {
                    $hours = "0$hours";
                }
                $block = str_replace($format, $hours, $block);
                $seekingBracket = false;
            }
        }
    }

    /** @param float|int $value value to be formatted */
    public static function format(mixed $value, string $format): string
    {
        // strip off first part containing e.g. [$-F800] or [$USD-409]
        // general syntax: [$<Currency string>-<language info>]
        // language info is in hexadecimal
        // strip off chinese part like [DBNum1][$-804]
        $format = (string) preg_replace('/^(\[DBNum\d\])*(\[\$[^\]]*\])/i', '', $format);

        // OpenOffice.org uses upper-case number formats, e.g. 'YYYY', convert to lower-case;
        //    but we don't want to change any quoted strings
        /** @var callable $callable */
        $callable = [self::class, 'setLowercaseCallback'];
        $format = (string) preg_replace_callback('/(?:^|")([^"]*)(?:$|")/', $callable, $format);

        // Only process the non-quoted blocks for date format characters

        $blocks = explode('"', $format);
        foreach ($blocks as $key => &$block) {
            if ($key % 2 == 0) {
                $block = strtr($block, self::DATE_FORMAT_REPLACEMENTS);
                if (!strpos($block, 'A')) {
                    // 24-hour time format
                    // when [h]:mm format, the [h] should replace to the hours of the value * 24
                    $seekingBracket = true;
                    self::tryInterval($seekingBracket, $block, $value, '[h]');
                    self::tryInterval($seekingBracket, $block, $value, '[hh]');
                    self::tryInterval($seekingBracket, $block, $value, '[mm]');
                    self::tryInterval($seekingBracket, $block, $value, '[m]');
                    self::tryInterval($seekingBracket, $block, $value, '[s]');
                    self::tryInterval($seekingBracket, $block, $value, '[ss]');
                    $block = strtr($block, self::DATE_FORMAT_REPLACEMENTS24);
                } else {
                    // 12-hour time format
                    $block = strtr($block, self::DATE_FORMAT_REPLACEMENTS12);
                }
            }
        }
        $format = implode('"', $blocks);

        // escape any quoted characters so that DateTime format() will render them correctly
        /** @var callable $callback */
        $callback = [self::class, 'escapeQuotesCallback'];
        $format = (string) preg_replace_callback('/"(.*)"/U', $callback, $format);

        try {
            $dateObj = Date::excelToDateTimeObject($value);
        } catch (Throwable) {
            return StringHelper::convertToString($value);
        }
        // If the colon preceding minute had been quoted, as happens in
        // Excel 2003 XML formats, m will not have been changed to i above.
        // Change it now.
        $format = (string) \preg_replace('/\\\:m/', ':i', $format);
        $microseconds = (int) $dateObj->format('u');
        if (str_contains($format, ':s.000')) {
            $milliseconds = (int) round($microseconds / 1000.0);
            if ($milliseconds === 1000) {
                $milliseconds = 0;
                $dateObj->modify('+1 second');
            }
            $dateObj->modify("-$microseconds microseconds");
            $format = str_replace(':s.000', ':s.' . sprintf('%03d', $milliseconds), $format);
        } elseif (str_contains($format, ':s.00')) {
            $centiseconds = (int) round($microseconds / 10000.0);
            if ($centiseconds === 100) {
                $centiseconds = 0;
                $dateObj->modify('+1 second');
            }
            $dateObj->modify("-$microseconds microseconds");
            $format = str_replace(':s.00', ':s.' . sprintf('%02d', $centiseconds), $format);
        } elseif (str_contains($format, ':s.0')) {
            $deciseconds = (int) round($microseconds / 100000.0);
            if ($deciseconds === 10) {
                $deciseconds = 0;
                $dateObj->modify('+1 second');
            }
            $dateObj->modify("-$microseconds microseconds");
            $format = str_replace(':s.0', ':s.' . sprintf('%1d', $deciseconds), $format);
        } else { // no fractional second
            if ($microseconds >= 500000) {
                $dateObj->modify('+1 second');
            }
            $dateObj->modify("-$microseconds microseconds");
        }

        return $dateObj->format($format);
    }

    /** @param string[] $matches */
    private static function setLowercaseCallback(array $matches): string
    {
        return mb_strtolower($matches[0]);
    }

    /** @param string[] $matches */
    private static function escapeQuotesCallback(array $matches): string
    {
        return '\\' . implode('\\', mb_str_split($matches[1], 1, 'UTF-8'));
    }
}
