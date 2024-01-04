<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Fill;

class CellFill
{
    /**
     * @var array<string, int>
     */
    protected static array $fillStyleMap = [
        Fill::FILL_NONE => 0x00,
        Fill::FILL_SOLID => 0x01,
        Fill::FILL_PATTERN_MEDIUMGRAY => 0x02,
        Fill::FILL_PATTERN_DARKGRAY => 0x03,
        Fill::FILL_PATTERN_LIGHTGRAY => 0x04,
        Fill::FILL_PATTERN_DARKHORIZONTAL => 0x05,
        Fill::FILL_PATTERN_DARKVERTICAL => 0x06,
        Fill::FILL_PATTERN_DARKDOWN => 0x07,
        Fill::FILL_PATTERN_DARKUP => 0x08,
        Fill::FILL_PATTERN_DARKGRID => 0x09,
        Fill::FILL_PATTERN_DARKTRELLIS => 0x0A,
        Fill::FILL_PATTERN_LIGHTHORIZONTAL => 0x0B,
        Fill::FILL_PATTERN_LIGHTVERTICAL => 0x0C,
        Fill::FILL_PATTERN_LIGHTDOWN => 0x0D,
        Fill::FILL_PATTERN_LIGHTUP => 0x0E,
        Fill::FILL_PATTERN_LIGHTGRID => 0x0F,
        Fill::FILL_PATTERN_LIGHTTRELLIS => 0x10,
        Fill::FILL_PATTERN_GRAY125 => 0x11,
        Fill::FILL_PATTERN_GRAY0625 => 0x12,
        Fill::FILL_GRADIENT_LINEAR => 0x00, // does not exist in BIFF8
        Fill::FILL_GRADIENT_PATH => 0x00,   // does not exist in BIFF8
    ];

    public static function style(Fill $fill): int
    {
        $fillStyle = $fill->getFillType();

        if (is_string($fillStyle) && array_key_exists($fillStyle, self::$fillStyleMap)) {
            return self::$fillStyleMap[$fillStyle];
        }

        return self::$fillStyleMap[Fill::FILL_NONE];
    }
}
