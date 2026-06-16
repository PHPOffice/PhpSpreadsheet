<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Border;

class CellBorder
{
    protected const BORDER_MAP = [
        Border::BORDER_NONE => 0x00,
        Border::BORDER_THIN => 0x01,
        Border::BORDER_MEDIUM => 0x02,
        Border::BORDER_DASHED => 0x03,
        Border::BORDER_DOTTED => 0x04,
        Border::BORDER_THICK => 0x05,
        Border::BORDER_DOUBLE => 0x06,
        Border::BORDER_HAIR => 0x07,
        Border::BORDER_MEDIUMDASHED => 0x08,
        Border::BORDER_DASHDOT => 0x09,
        Border::BORDER_MEDIUMDASHDOT => 0x0A,
        Border::BORDER_DASHDOTDOT => 0x0B,
        Border::BORDER_MEDIUMDASHDOTDOT => 0x0C,
        Border::BORDER_SLANTDASHDOT => 0x0D,
        Border::BORDER_OMIT => 0x00,
    ];

    public static function style(Border $border): int
    {
        $borderStyle = $border->getBorderStyle();

        return self::BORDER_MAP[$borderStyle] ?? self::BORDER_MAP[Border::BORDER_NONE];
    }
}
