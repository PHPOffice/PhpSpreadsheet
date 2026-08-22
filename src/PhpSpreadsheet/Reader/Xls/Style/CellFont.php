<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Font;

class CellFont
{
    public static function escapement(Font $font, int $escapement): void
    {
        switch ($escapement) {
            case 0x0001:
                $font->setSuperscript(true);

                break;
            case 0x0002:
                $font->setSubscript(true);

                break;
        }
    }

    /**
     * @var array<int, string>
     */
    protected static array $underlineMap = [
        0x01 => Font::UNDERLINE_SINGLE,
        0x02 => Font::UNDERLINE_DOUBLE,
        0x21 => Font::UNDERLINE_SINGLEACCOUNTING,
        0x22 => Font::UNDERLINE_DOUBLEACCOUNTING,
    ];

    public static function underline(Font $font, int $underline): void
    {
        if (array_key_exists($underline, self::$underlineMap)) {
            $font->setUnderline(self::$underlineMap[$underline]);
        }
    }
}
