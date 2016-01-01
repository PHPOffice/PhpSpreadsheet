<?php

namespace PHPExcel\Reader\Excel5\Style;

use \PHPExcel\Style\Border;

class Border
{
    protected static $map = array(
        0x00 => Border::BORDER_NONE,
        0x01 => Border::BORDER_THIN,
        0x02 => Border::BORDER_MEDIUM,
        0x03 => Border::BORDER_DASHED,
        0x04 => Border::BORDER_DOTTED,
        0x05 => Border::BORDER_THICK,
        0x06 => Border::BORDER_DOUBLE,
        0x07 => Border::BORDER_HAIR,
        0x08 => Border::BORDER_MEDIUMDASHED,
        0x09 => Border::BORDER_DASHDOT,
        0x0A => Border::BORDER_MEDIUMDASHDOT,
        0x0B => Border::BORDER_DASHDOTDOT,
        0x0C => Border::BORDER_MEDIUMDASHDOTDOT,
        0x0D => Border::BORDER_SLANTDASHDOT,
    );

    /**
     * Map border style
     * OpenOffice documentation: 2.5.11
     *
     * @param int $index
     * @return string
     */
    public static function lookup($index)
    {
        if (isset(self::$map[$index])) {
            return self::$map[$index];
        }
        return Border::BORDER_NONE;
    }
}