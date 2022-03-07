<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls\Style;

use PhpOffice\PhpSpreadsheet\Style\Color;

class ColorMap
{
    /**
     * @var array<string, int>
     */
    private static $colorMap = [
        '#000000' => 0x08,
        '#FFFFFF' => 0x09,
        '#FF0000' => 0x0A,
        '#00FF00' => 0x0B,
        '#0000FF' => 0x0C,
        '#FFFF00' => 0x0D,
        '#FF00FF' => 0x0E,
        '#00FFFF' => 0x0F,
        '#800000' => 0x10,
        '#008000' => 0x11,
        '#000080' => 0x12,
        '#808000' => 0x13,
        '#800080' => 0x14,
        '#008080' => 0x15,
        '#C0C0C0' => 0x16,
        '#808080' => 0x17,
        '#9999FF' => 0x18,
        '#993366' => 0x19,
        '#FFFFCC' => 0x1A,
        '#CCFFFF' => 0x1B,
        '#660066' => 0x1C,
        '#FF8080' => 0x1D,
        '#0066CC' => 0x1E,
        '#CCCCFF' => 0x1F,
        //        '#000080' => 0x20,
        //        '#FF00FF' => 0x21,
        //        '#FFFF00' => 0x22,
        //        '#00FFFF' => 0x23,
        //        '#800080' => 0x24,
        //        '#800000' => 0x25,
        //        '#008080' => 0x26,
        //        '#0000FF' => 0x27,
        '#00CCFF' => 0x28,
        //        '#CCFFFF' => 0x29,
        '#CCFFCC' => 0x2A,
        '#FFFF99' => 0x2B,
        '#99CCFF' => 0x2C,
        '#FF99CC' => 0x2D,
        '#CC99FF' => 0x2E,
        '#FFCC99' => 0x2F,
        '#3366FF' => 0x30,
        '#33CCCC' => 0x31,
        '#99CC00' => 0x32,
        '#FFCC00' => 0x33,
        '#FF9900' => 0x34,
        '#FF6600' => 0x35,
        '#666699' => 0x36,
        '#969696' => 0x37,
        '#003366' => 0x38,
        '#339966' => 0x39,
        '#003300' => 0x3A,
        '#333300' => 0x3B,
        '#993300' => 0x3C,
        //        '#993366' => 0x3D,
        '#333399' => 0x3E,
        '#333333' => 0x3F,
    ];

    public static function lookup(Color $color, int $defaultIndex = 0x00): int
    {
        $colorRgb = $color->getRGB();
        if (is_string($colorRgb) && array_key_exists("#{$colorRgb}", self::$colorMap)) {
            return self::$colorMap["#{$colorRgb}"];
        }

//      TODO Try and map RGB value to nearest colour within the define pallette
//        $red =  Color::getRed($colorRgb, false);
//        $green = Color::getGreen($colorRgb, false);
//        $blue = Color::getBlue($colorRgb, false);

//        $paletteSpace = 3;
//        $newColor = ($red * $paletteSpace / 256) * ($paletteSpace * $paletteSpace) +
//            ($green * $paletteSpace / 256) * $paletteSpace +
//            ($blue * $paletteSpace / 256);

        return $defaultIndex;
    }
}
