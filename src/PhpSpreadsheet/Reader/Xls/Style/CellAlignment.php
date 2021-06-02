<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CellAlignment
{
    /**
     * @var array<int, string>
     */
    protected static $horizontalAlignmentMap = [
        0 => Alignment::HORIZONTAL_GENERAL,
        1 => Alignment::HORIZONTAL_LEFT,
        2 => Alignment::HORIZONTAL_CENTER,
        3 => Alignment::HORIZONTAL_RIGHT,
        4 => Alignment::HORIZONTAL_FILL,
        5 => Alignment::HORIZONTAL_JUSTIFY,
        6 => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
    ];

    /**
     * @var array<int, string>
     */
    protected static $verticalAlignmentMap = [
        0 => Alignment::VERTICAL_TOP,
        1 => Alignment::VERTICAL_CENTER,
        2 => Alignment::VERTICAL_BOTTOM,
        3 => Alignment::VERTICAL_JUSTIFY,
    ];

    public static function horizontal(Alignment $alignment, int $horizontal): void
    {
        if (array_key_exists($horizontal, self::$horizontalAlignmentMap)) {
            $alignment->setHorizontal(self::$horizontalAlignmentMap[$horizontal]);
        }
    }

    public static function vertical(Alignment $alignment, int $vertical): void
    {
        if (array_key_exists($vertical, self::$verticalAlignmentMap)) {
            $alignment->setVertical(self::$verticalAlignmentMap[$vertical]);
        }
    }

    public static function wrap(Alignment $alignment, int $wrap): void
    {
        $alignment->setWrapText((bool) $wrap);
    }
}
