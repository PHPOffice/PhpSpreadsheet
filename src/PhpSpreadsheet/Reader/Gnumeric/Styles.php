<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use SimpleXMLElement;

class Styles
{
    private Spreadsheet $spreadsheet;

    protected bool $readDataOnly;

    /** @var array<string, string[]> */
    public static array $mappings = [
        'borderStyle' => [
            '0' => Border::BORDER_NONE,
            '1' => Border::BORDER_THIN,
            '2' => Border::BORDER_MEDIUM,
            '3' => Border::BORDER_SLANTDASHDOT,
            '4' => Border::BORDER_DASHED,
            '5' => Border::BORDER_THICK,
            '6' => Border::BORDER_DOUBLE,
            '7' => Border::BORDER_DOTTED,
            '8' => Border::BORDER_MEDIUMDASHED,
            '9' => Border::BORDER_DASHDOT,
            '10' => Border::BORDER_MEDIUMDASHDOT,
            '11' => Border::BORDER_DASHDOTDOT,
            '12' => Border::BORDER_MEDIUMDASHDOTDOT,
            '13' => Border::BORDER_MEDIUMDASHDOTDOT,
        ],
        'fillType' => [
            '1' => Fill::FILL_SOLID,
            '2' => Fill::FILL_PATTERN_DARKGRAY,
            '3' => Fill::FILL_PATTERN_MEDIUMGRAY,
            '4' => Fill::FILL_PATTERN_LIGHTGRAY,
            '5' => Fill::FILL_PATTERN_GRAY125,
            '6' => Fill::FILL_PATTERN_GRAY0625,
            '7' => Fill::FILL_PATTERN_DARKHORIZONTAL, // horizontal stripe
            '8' => Fill::FILL_PATTERN_DARKVERTICAL, // vertical stripe
            '9' => Fill::FILL_PATTERN_DARKDOWN, // diagonal stripe
            '10' => Fill::FILL_PATTERN_DARKUP, // reverse diagonal stripe
            '11' => Fill::FILL_PATTERN_DARKGRID, // diagoanl crosshatch
            '12' => Fill::FILL_PATTERN_DARKTRELLIS, // thick diagonal crosshatch
            '13' => Fill::FILL_PATTERN_LIGHTHORIZONTAL,
            '14' => Fill::FILL_PATTERN_LIGHTVERTICAL,
            '15' => Fill::FILL_PATTERN_LIGHTUP,
            '16' => Fill::FILL_PATTERN_LIGHTDOWN,
            '17' => Fill::FILL_PATTERN_LIGHTGRID, // thin horizontal crosshatch
            '18' => Fill::FILL_PATTERN_LIGHTTRELLIS, // thin diagonal crosshatch
        ],
        'horizontal' => [
            '1' => Alignment::HORIZONTAL_GENERAL,
            '2' => Alignment::HORIZONTAL_LEFT,
            '4' => Alignment::HORIZONTAL_RIGHT,
            '8' => Alignment::HORIZONTAL_CENTER,
            '16' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            '32' => Alignment::HORIZONTAL_JUSTIFY,
            '64' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
        ],
        'underline' => [
            '1' => Font::UNDERLINE_SINGLE,
            '2' => Font::UNDERLINE_DOUBLE,
            '3' => Font::UNDERLINE_SINGLEACCOUNTING,
            '4' => Font::UNDERLINE_DOUBLEACCOUNTING,
        ],
        'vertical' => [
            '1' => Alignment::VERTICAL_TOP,
            '2' => Alignment::VERTICAL_BOTTOM,
            '4' => Alignment::VERTICAL_CENTER,
            '8' => Alignment::VERTICAL_JUSTIFY,
        ],
    ];

    public function __construct(Spreadsheet $spreadsheet, bool $readDataOnly)
    {
        $this->spreadsheet = $spreadsheet;
        $this->readDataOnly = $readDataOnly;
    }

    public function read(SimpleXMLElement $sheet, int $maxRow, int $maxCol): void
    {
        if ($sheet->Styles->StyleRegion !== null) {
            $this->readStyles($sheet->Styles->StyleRegion, $maxRow, $maxCol);
        }
    }

    private function readStyles(SimpleXMLElement $styleRegion, int $maxRow, int $maxCol): void
    {
        foreach ($styleRegion as $style) {
            $styleAttributes = $style->attributes();
            if ($styleAttributes !== null && ($styleAttributes['startRow'] <= $maxRow) && ($styleAttributes['startCol'] <= $maxCol)) {
                $cellRange = $this->readStyleRange($styleAttributes, $maxCol, $maxRow);

                $styleAttributes = $style->Style->attributes();

                /** @var mixed[][] */
                $styleArray = [];
                // We still set the number format mask for date/time values, even if readDataOnly is true
                //    so that we can identify whether a float is a float or a date value
                $formatCode = $styleAttributes ? (string) $styleAttributes['Format'] : null;
                if ($formatCode && Date::isDateTimeFormatCode($formatCode)) {
                    $styleArray['numberFormat']['formatCode'] = $formatCode;
                }
                if ($this->readDataOnly === false && $styleAttributes !== null) {
                    //    If readDataOnly is false, we set all formatting information
                    $styleArray['numberFormat']['formatCode'] = $formatCode;
                    $styleArray = $this->readStyle($styleArray, $styleAttributes, $style);
                }
                /** @var mixed[][] $styleArray */
                $this->spreadsheet
                    ->getActiveSheet()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);
            }
        }
    }

    /** @param mixed[][] $styleArray */
    private function addBorderDiagonal(SimpleXMLElement $srssb, array &$styleArray): void
    {
        if (isset($srssb->Diagonal, $srssb->{'Rev-Diagonal'})) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->Diagonal->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_BOTH;
        } elseif (isset($srssb->Diagonal)) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->Diagonal->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_UP;
        } elseif (isset($srssb->{'Rev-Diagonal'})) {
            $styleArray['borders']['diagonal'] = self::parseBorderAttributes($srssb->{'Rev-Diagonal'}->attributes());
            $styleArray['borders']['diagonalDirection'] = Borders::DIAGONAL_DOWN;
        }
    }

    /** @param mixed[][] $styleArray */
    private function addBorderStyle(SimpleXMLElement $srssb, array &$styleArray, string $direction): void
    {
        $ucDirection = ucfirst($direction);
        if (isset($srssb->$ucDirection)) {
            /** @var SimpleXMLElement */
            $temp = $srssb->$ucDirection;
            $styleArray['borders'][$direction] = self::parseBorderAttributes($temp->attributes());
        }
    }

    private function calcRotation(SimpleXMLElement $styleAttributes): int
    {
        $rotation = (int) $styleAttributes->Rotation;
        if ($rotation >= 270 && $rotation <= 360) {
            $rotation -= 360;
        }
        $rotation = (abs($rotation) > 90) ? 0 : $rotation;

        return $rotation;
    }

    /** @param mixed[][] $styleArray */
    private static function addStyle(array &$styleArray, string $key, string $value): void
    {
        if (array_key_exists($value, self::$mappings[$key])) {
            $styleArray[$key] = self::$mappings[$key][$value]; //* @phpstan-ignore-line
        }
    }

    /** @param mixed[][] $styleArray */
    private static function addStyle2(array &$styleArray, string $key1, string $key, string $value): void
    {
        if (array_key_exists($value, self::$mappings[$key])) {
            $styleArray[$key1][$key] = self::$mappings[$key][$value];
        }
    }

    /** @return mixed[][] */
    private static function parseBorderAttributes(?SimpleXMLElement $borderAttributes): array
    {
        /** @var mixed[][] */
        $styleArray = [];
        if ($borderAttributes !== null) {
            if (isset($borderAttributes['Color'])) {
                $styleArray['color']['rgb'] = self::parseGnumericColour($borderAttributes['Color']);
            }

            self::addStyle($styleArray, 'borderStyle', (string) $borderAttributes['Style']);
        }
        /** @var mixed[][] $styleArray */

        return $styleArray;
    }

    private static function parseGnumericColour(string $gnmColour): string
    {
        [$gnmR, $gnmG, $gnmB] = explode(':', $gnmColour);
        $gnmR = substr(str_pad($gnmR, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmG = substr(str_pad($gnmG, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmB = substr(str_pad($gnmB, 4, '0', STR_PAD_RIGHT), 0, 2);

        return $gnmR . $gnmG . $gnmB;
    }

    /** @param mixed[][] $styleArray */
    private function addColors(array &$styleArray, SimpleXMLElement $styleAttributes): void
    {
        $RGB = self::parseGnumericColour((string) $styleAttributes['Fore']);
        /** @var mixed[][][] $styleArray */
        $styleArray['font']['color']['rgb'] = $RGB;
        $RGB = self::parseGnumericColour((string) $styleAttributes['Back']);
        $shade = (string) $styleAttributes['Shade'];
        if (($RGB !== '000000') || ($shade !== '0')) {
            $RGB2 = self::parseGnumericColour((string) $styleAttributes['PatternColor']);
            if ($shade === '1') {
                $styleArray['fill']['startColor']['rgb'] = $RGB;
                $styleArray['fill']['endColor']['rgb'] = $RGB2;
            } else {
                $styleArray['fill']['endColor']['rgb'] = $RGB;
                $styleArray['fill']['startColor']['rgb'] = $RGB2;
            }
            self::addStyle2($styleArray, 'fill', 'fillType', $shade);
        }
    }

    private function readStyleRange(SimpleXMLElement $styleAttributes, int $maxCol, int $maxRow): string
    {
        $startColumn = Coordinate::stringFromColumnIndex((int) $styleAttributes['startCol'] + 1);
        $startRow = $styleAttributes['startRow'] + 1;

        $endColumn = ($styleAttributes['endCol'] > $maxCol) ? $maxCol : (int) $styleAttributes['endCol'];
        $endColumn = Coordinate::stringFromColumnIndex($endColumn + 1);

        $endRow = 1 + (($styleAttributes['endRow'] > $maxRow) ? $maxRow : (int) $styleAttributes['endRow']);
        $cellRange = $startColumn . $startRow . ':' . $endColumn . $endRow;

        return $cellRange;
    }

    /**
     * @param mixed[][] $styleArray
     *
     * @return mixed[]
     */
    private function readStyle(array $styleArray, SimpleXMLElement $styleAttributes, SimpleXMLElement $style): array
    {
        self::addStyle2($styleArray, 'alignment', 'horizontal', (string) $styleAttributes['HAlign']);
        self::addStyle2($styleArray, 'alignment', 'vertical', (string) $styleAttributes['VAlign']);
        $styleArray['alignment']['wrapText'] = $styleAttributes['WrapText'] == '1';
        $styleArray['alignment']['textRotation'] = $this->calcRotation($styleAttributes);
        $styleArray['alignment']['shrinkToFit'] = $styleAttributes['ShrinkToFit'] == '1';
        $styleArray['alignment']['indent'] = ((int) ($styleAttributes['Indent']) > 0) ? $styleAttributes['indent'] : 0;

        $this->addColors($styleArray, $styleAttributes);

        $fontAttributes = $style->Style->Font->attributes();
        if ($fontAttributes !== null) {
            $styleArray['font']['name'] = (string) $style->Style->Font;
            $styleArray['font']['size'] = (int) ($fontAttributes['Unit']);
            $styleArray['font']['bold'] = $fontAttributes['Bold'] == '1';
            $styleArray['font']['italic'] = $fontAttributes['Italic'] == '1';
            $styleArray['font']['strikethrough'] = $fontAttributes['StrikeThrough'] == '1';
            self::addStyle2($styleArray, 'font', 'underline', (string) $fontAttributes['Underline']);

            switch ($fontAttributes['Script']) {
                case '1':
                    $styleArray['font']['superscript'] = true;

                    break;
                case '-1':
                    $styleArray['font']['subscript'] = true;

                    break;
            }
        }

        if (isset($style->Style->StyleBorder)) {
            $srssb = $style->Style->StyleBorder;
            $this->addBorderStyle($srssb, $styleArray, 'top');
            $this->addBorderStyle($srssb, $styleArray, 'bottom');
            $this->addBorderStyle($srssb, $styleArray, 'left');
            $this->addBorderStyle($srssb, $styleArray, 'right');
            $this->addBorderDiagonal($srssb, $styleArray);
        }
        //    TO DO
        /*
        if (isset($style->Style->HyperLink)) {
            $hyperlink = $style->Style->HyperLink->attributes();
        }
        */

        return $styleArray;
    }
}
