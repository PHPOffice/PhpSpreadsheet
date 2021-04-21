<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CellAlignment
{
    private static $horizontalMap = [
        Alignment::HORIZONTAL_GENERAL => 0,
        Alignment::HORIZONTAL_LEFT => 1,
        Alignment::HORIZONTAL_RIGHT => 3,
        Alignment::HORIZONTAL_CENTER => 2,
        Alignment::HORIZONTAL_CENTER_CONTINUOUS => 6,
        Alignment::HORIZONTAL_JUSTIFY => 5,
    ];

    private static $verticalMap = [
        Alignment::VERTICAL_BOTTOM => 2 << 4,
        Alignment::VERTICAL_TOP => 0 << 4,
        Alignment::VERTICAL_CENTER => 1 << 4,
        Alignment::VERTICAL_JUSTIFY => 3 << 4,
    ];

    public static function horizontal(Alignment $alignment): int
    {
        $horizontalAlignment = $alignment->getHorizontal();

        if (is_string($horizontalAlignment) && array_key_exists($horizontalAlignment, self::$horizontalMap)) {
            return self::$horizontalMap[$horizontalAlignment];
        }

        return Alignment::HORIZONTAL_GENERAL;
    }

    public static function wrap(Alignment $alignment)
    {
        $wrap = $alignment->getWrapText();

        return ($wrap === true) ? 1 << 3 : 0 << 3;
    }

    public static function vertical(Alignment $alignment): int
    {
        $verticalAlignment = $alignment->getVertical();

        if (is_string($verticalAlignment) && array_key_exists($verticalAlignment, self::$verticalMap)) {
            return self::$verticalMap[$verticalAlignment];
        }

        return Alignment::HORIZONTAL_GENERAL;
    }
}
