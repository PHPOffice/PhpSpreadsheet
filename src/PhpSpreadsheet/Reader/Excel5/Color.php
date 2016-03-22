<?php

namespace PHPExcel\Reader\Excel5;

class Color
{
    /**
     * Read color
     *
     * @param int $color Indexed color
     * @param array $palette Color palette
     * @return array RGB color value, example: array('rgb' => 'FF0000')
     */
    public static function map($color, $palette, $version)
    {
        if ($color <= 0x07 || $color >= 0x40) {
            // special built-in color
            return Color\BuiltIn::lookup($color);
        } elseif (isset($palette) && isset($palette[$color - 8])) {
            // palette color, color index 0x08 maps to pallete index 0
            return $palette[$color - 8];
        } else {
            // default color table
            if ($version == \PHPExcel\Reader\Excel5::XLS_BIFF8) {
                return Color\BIFF8::lookup($color);
            } else {
                // BIFF5
                return Color\BIFF5::lookup($color);
            }
        }

        return $color;
    }
}
