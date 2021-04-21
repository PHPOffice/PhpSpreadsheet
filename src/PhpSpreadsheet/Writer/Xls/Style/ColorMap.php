<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls\Style;

class ColorMap
{
    public static function lookup(\PhpOffice\PhpSpreadsheet\Style\Color $color, int $default = 0x00): int
    {
        $colorRgb = $color->getRGB();

        switch ($colorRgb) {
            case '000000':
                $colorIdx = 0x08;

                break;
            case 'FFFFFF':
                $colorIdx = 0x09;

                break;
            case 'FF0000':
                $colorIdx = 0x0A;

                break;
            case '00FF00':
                $colorIdx = 0x0B;

                break;
            case '0000FF':
                $colorIdx = 0x0C;

                break;
            case 'FFFF00':
                $colorIdx = 0x0D;

                break;
            case 'FF00FF':
                $colorIdx = 0x0E;

                break;
            case '00FFFF':
                $colorIdx = 0x0F;

                break;
            case '800000':
                $colorIdx = 0x10;

                break;
            case '008000':
                $colorIdx = 0x11;

                break;
            case '000080':
                $colorIdx = 0x12;

                break;
            case '808000':
                $colorIdx = 0x13;

                break;
            case '800080':
                $colorIdx = 0x14;

                break;
            case '008080':
                $colorIdx = 0x15;

                break;
            case 'C0C0C0':
                $colorIdx = 0x16;

                break;
            case '808080':
                $colorIdx = 0x17;

                break;
            case '9999FF':
                $colorIdx = 0x18;

                break;
            case '993366':
                $colorIdx = 0x19;

                break;
            case 'FFFFCC':
                $colorIdx = 0x1A;

                break;
            case 'CCFFFF':
                $colorIdx = 0x1B;

                break;
            case '660066':
                $colorIdx = 0x1C;

                break;
            case 'FF8080':
                $colorIdx = 0x1D;

                break;
            case '0066CC':
                $colorIdx = 0x1E;

                break;
            case 'CCCCFF':
                $colorIdx = 0x1F;

                break;
            case '000080':
                $colorIdx = 0x20;

                break;
            case 'FF00FF':
                $colorIdx = 0x21;

                break;
            case 'FFFF00':
                $colorIdx = 0x22;

                break;
            case '00FFFF':
                $colorIdx = 0x23;

                break;
            case '800080':
                $colorIdx = 0x24;

                break;
            case '800000':
                $colorIdx = 0x25;

                break;
            case '008080':
                $colorIdx = 0x26;

                break;
            case '0000FF':
                $colorIdx = 0x27;

                break;
            case '00CCFF':
                $colorIdx = 0x28;

                break;
            case 'CCFFFF':
                $colorIdx = 0x29;

                break;
            case 'CCFFCC':
                $colorIdx = 0x2A;

                break;
            case 'FFFF99':
                $colorIdx = 0x2B;

                break;
            case '99CCFF':
                $colorIdx = 0x2C;

                break;
            case 'FF99CC':
                $colorIdx = 0x2D;

                break;
            case 'CC99FF':
                $colorIdx = 0x2E;

                break;
            case 'FFCC99':
                $colorIdx = 0x2F;

                break;
            case '3366FF':
                $colorIdx = 0x30;

                break;
            case '33CCCC':
                $colorIdx = 0x31;

                break;
            case '99CC00':
                $colorIdx = 0x32;

                break;
            case 'FFCC00':
                $colorIdx = 0x33;

                break;
            case 'FF9900':
                $colorIdx = 0x34;

                break;
            case 'FF6600':
                $colorIdx = 0x35;

                break;
            case '666699':
                $colorIdx = 0x36;

                break;
            case '969696':
                $colorIdx = 0x37;

                break;
            case '003366':
                $colorIdx = 0x38;

                break;
            case '339966':
                $colorIdx = 0x39;

                break;
            case '003300':
                $colorIdx = 0x3A;

                break;
            case '333300':
                $colorIdx = 0x3B;

                break;
            case '993300':
                $colorIdx = 0x3C;

                break;
            case '993366':
                $colorIdx = 0x3D;

                break;
            case '333399':
                $colorIdx = 0x3E;

                break;
            case '333333':
                $colorIdx = 0x3F;

                break;
            default:
                $colorIdx = $default;

                break;
        }

        return $colorIdx;
    }
}
