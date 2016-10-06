<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Fill;

class FillPattern
{
    protected static $map = [
        0x00 => Fill::FILL_NONE,
        0x01 => Fill::FILL_SOLID,
        0x02 => Fill::FILL_PATTERN_MEDIUMGRAY,
        0x03 => Fill::FILL_PATTERN_DARKGRAY,
        0x04 => Fill::FILL_PATTERN_LIGHTGRAY,
        0x05 => Fill::FILL_PATTERN_DARKHORIZONTAL,
        0x06 => Fill::FILL_PATTERN_DARKVERTICAL,
        0x07 => Fill::FILL_PATTERN_DARKDOWN,
        0x08 => Fill::FILL_PATTERN_DARKUP,
        0x09 => Fill::FILL_PATTERN_DARKGRID,
        0x0A => Fill::FILL_PATTERN_DARKTRELLIS,
        0x0B => Fill::FILL_PATTERN_LIGHTHORIZONTAL,
        0x0C => Fill::FILL_PATTERN_LIGHTVERTICAL,
        0x0D => Fill::FILL_PATTERN_LIGHTDOWN,
        0x0E => Fill::FILL_PATTERN_LIGHTUP,
        0x0F => Fill::FILL_PATTERN_LIGHTGRID,
        0x10 => Fill::FILL_PATTERN_LIGHTTRELLIS,
        0x11 => Fill::FILL_PATTERN_GRAY125,
        0x12 => Fill::FILL_PATTERN_GRAY0625,
    ];

    /**
     * Get fill pattern from index
     * OpenOffice documentation: 2.5.12
     *
     * @param int $index
     * @return string
     */
    public static function lookup($index)
    {
        if (isset(self::$map[$index])) {
            return self::$map[$index];
        }

        return Fill::FILL_NONE;
    }
}
