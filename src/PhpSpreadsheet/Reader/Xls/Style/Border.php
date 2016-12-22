<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Border as StyleBorder;

class Border
{
    protected static $map = [
        0x00 => StyleBorder::BORDER_NONE,
        0x01 => StyleBorder::BORDER_THIN,
        0x02 => StyleBorder::BORDER_MEDIUM,
        0x03 => StyleBorder::BORDER_DASHED,
        0x04 => StyleBorder::BORDER_DOTTED,
        0x05 => StyleBorder::BORDER_THICK,
        0x06 => StyleBorder::BORDER_DOUBLE,
        0x07 => StyleBorder::BORDER_HAIR,
        0x08 => StyleBorder::BORDER_MEDIUMDASHED,
        0x09 => StyleBorder::BORDER_DASHDOT,
        0x0A => StyleBorder::BORDER_MEDIUMDASHDOT,
        0x0B => StyleBorder::BORDER_DASHDOTDOT,
        0x0C => StyleBorder::BORDER_MEDIUMDASHDOTDOT,
        0x0D => StyleBorder::BORDER_SLANTDASHDOT,
    ];

    /**
     * Map border style
     * OpenOffice documentation: 2.5.11.
     *
     * @param int $index
     *
     * @return string
     */
    public static function lookup($index)
    {
        if (isset(self::$map[$index])) {
            return self::$map[$index];
        }

        return StyleBorder::BORDER_NONE;
    }
}
