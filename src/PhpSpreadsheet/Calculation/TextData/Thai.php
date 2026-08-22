<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Thai
{
    use ArrayEnabled;

    private const THAI_DIGITS = [
        0 => 'ศูนย์',
        1 => 'หนึ่ง',
        2 => 'สอง',
        3 => 'สาม',
        4 => 'สี่',
        5 => 'ห้า',
        6 => 'หก',
        7 => 'เจ็ด',
        8 => 'แปด',
        9 => 'เก้า',
    ];

    private const THAI_UNITS = [
        1 => 'สิบ',
        2 => 'ร้อย',
        3 => 'พัน',
        4 => 'หมื่น',
        5 => 'แสน',
        6 => 'ล้าน',
    ];

    private const THAI_COMPOUND_ONE = 'เอ็ด';
    private const THAI_COMPOUND_TWO = 'ยี่';
    private const THAI_INTEGER = 'ถ้วน';
    private const THAI_MINUS = 'ลบ';
    private const THAI_BAHT = 'บาท';
    private const THAI_SATANG = 'สตางค์';

    /**
     * BAHTTEXT.
     *
     * @param mixed $number The number or array of numbers to convert
     *
     * @return array<mixed>|string If an array of values is passed as the argument, then the returned result will also be an array with the same dimensions
     */
    public static function getBahtText(mixed $number): array|string
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        if (is_string($number) && preg_match('/^-?\d+$/', $number)) {
            $isNegative = str_starts_with($number, '-');
            $baht = ltrim($number, '-0') ?: '0';
            $satang = '00';
        } elseif (is_bool($number) || is_numeric($number)) {
            $number += 0;
            $isNegative = $number < 0;
            [$baht, $satang] = explode('.', number_format(abs($number), 2, '.', ''));
        } else {
            return ExcelError::VALUE();
        }

        $hasWhole = $baht !== '0';
        $hasFraction = $satang !== '00';

        if (!$hasWhole && !$hasFraction) {
            return self::THAI_DIGITS[0] . self::THAI_BAHT . self::THAI_INTEGER;
        }

        $text = $isNegative
            ? self::THAI_MINUS
            : '';

        if ($hasWhole) {
            $text .= self::convertLarge($baht) . self::THAI_BAHT;
        }

        $text .= $hasFraction
            ? self::convertBlock($satang) . self::THAI_SATANG
            : self::THAI_INTEGER;

        return $text;
    }

    private static function convertLarge(string $digits): string
    {
        $length = strlen($digits) % 6 ?: 6;

        $chunks = [
            substr($digits, 0, $length),
            ...str_split(substr($digits, $length), 6),
        ];

        $chunks = array_filter($chunks, fn (string $chunk): bool => $chunk !== '');

        return implode(
            self::THAI_UNITS[6],
            array_map(self::convertBlock(...), $chunks)
        );
    }

    private static function convertBlock(string $block): string
    {
        $out = '';
        $length = strlen($block);
        $i = 0;

        // Hundreds and higher powers
        for ($power = $length - 1; $power >= 2; --$power) {
            $digit = $block[$i++];
            if ($digit !== '0') {
                $out .= self::THAI_DIGITS[$digit] . self::THAI_UNITS[$power];
            }
        }

        // Tens
        $ten = $length > 1 ? $block[$i++] : '0';
        if ($ten !== '0') {
            $out .= match ($ten) {
                '1' => '',
                '2' => self::THAI_COMPOUND_TWO,
                default => self::THAI_DIGITS[$ten],
            } . self::THAI_UNITS[1];
        }

        // Ones
        $one = $block[$i] ?? '0';
        if ($one !== '0') {
            $out .= $ten !== '0' && $one === '1'
                ? self::THAI_COMPOUND_ONE
                : self::THAI_DIGITS[$one];
        }

        return $out;
    }
}
