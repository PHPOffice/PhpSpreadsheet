<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Color
{
    /**
     * Read color.
     *
     * @param int $color Indexed color
     * @param string[][] $palette Color palette
     *
     * @return string[] RGB color value, example: ['rgb' => 'FF0000']
     */
    public static function map(int $color, array $palette, int $version): array
    {
        if ($color <= 0x07 || $color >= 0x40) {
            // special built-in color
            return Color\BuiltIn::lookup($color);
        } elseif (isset($palette[$color - 8])) {
            // palette color, color index 0x08 maps to pallete index 0
            return $palette[$color - 8];
        }

        return ($version === Xls::XLS_BIFF8) ? Color\BIFF8::lookup($color) : Color\BIFF5::lookup($color);
    }
}
