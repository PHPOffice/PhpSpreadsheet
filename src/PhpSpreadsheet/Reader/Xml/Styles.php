<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use SimpleXMLElement;

class Styles
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * Formats.
     *
     * @var array
     */
    protected $styles = [];

    private static $mappings = [
        'borderStyle' => [
            '1continuous' => Border::BORDER_THIN,
            '1dash' => Border::BORDER_DASHED,
            '1dashdot' => Border::BORDER_DASHDOT,
            '1dashdotdot' => Border::BORDER_DASHDOTDOT,
            '1dot' => Border::BORDER_DOTTED,
            '1double' => Border::BORDER_DOUBLE,
            '2continuous' => Border::BORDER_MEDIUM,
            '2dash' => Border::BORDER_MEDIUMDASHED,
            '2dashdot' => Border::BORDER_MEDIUMDASHDOT,
            '2dashdotdot' => Border::BORDER_MEDIUMDASHDOTDOT,
            '2dot' => Border::BORDER_DOTTED,
            '2double' => Border::BORDER_DOUBLE,
            '3continuous' => Border::BORDER_THICK,
            '3dash' => Border::BORDER_MEDIUMDASHED,
            '3dashdot' => Border::BORDER_MEDIUMDASHDOT,
            '3dashdotdot' => Border::BORDER_MEDIUMDASHDOTDOT,
            '3dot' => Border::BORDER_DOTTED,
            '3double' => Border::BORDER_DOUBLE,
        ],
        'fillType' => [
            'solid' => Fill::FILL_SOLID,
            'gray75' => Fill::FILL_PATTERN_DARKGRAY,
            'gray50' => Fill::FILL_PATTERN_MEDIUMGRAY,
            'gray25' => Fill::FILL_PATTERN_LIGHTGRAY,
            'gray125' => Fill::FILL_PATTERN_GRAY125,
            'gray0625' => Fill::FILL_PATTERN_GRAY0625,
            'horzstripe' => Fill::FILL_PATTERN_DARKHORIZONTAL, // horizontal stripe
            'vertstripe' => Fill::FILL_PATTERN_DARKVERTICAL, // vertical stripe
            'reversediagstripe' => Fill::FILL_PATTERN_DARKUP, // reverse diagonal stripe
            'diagstripe' => Fill::FILL_PATTERN_DARKDOWN, // diagonal stripe
            'diagcross' => Fill::FILL_PATTERN_DARKGRID, // diagoanl crosshatch
            'thickdiagcross' => Fill::FILL_PATTERN_DARKTRELLIS, // thick diagonal crosshatch
            'thinhorzstripe' => Fill::FILL_PATTERN_LIGHTHORIZONTAL,
            'thinvertstripe' => Fill::FILL_PATTERN_LIGHTVERTICAL,
            'thinreversediagstripe' => Fill::FILL_PATTERN_LIGHTUP,
            'thindiagstripe' => Fill::FILL_PATTERN_LIGHTDOWN,
            'thinhorzcross' => Fill::FILL_PATTERN_LIGHTGRID, // thin horizontal crosshatch
            'thindiagcross' => Fill::FILL_PATTERN_LIGHTTRELLIS, // thin diagonal crosshatch
        ],
    ];

    public static function xmlMappings(): array
    {
        return self::$mappings;
    }

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function parseStyles(SimpleXMLElement $xml, array $namespaces): array
    {
        if (!isset($xml->Styles)) {
            return [];
        }

        foreach ($xml->Styles[0] as $style) {
            $style_ss = self::getAttributes($style, $namespaces['ss']);
            $styleID = (string) $style_ss['ID'];
            $this->styles[$styleID] = $this->styles['Default'] ?? [];
            foreach ($style as $styleType => $styleDatax) {
                $styleData = $styleDatax ?? new SimpleXMLElement('<xml></xml>');
                $styleAttributes = $styleData->attributes($namespaces['ss']);
                switch ($styleType) {
                    case 'Alignment':
                        $this->parseStyleAlignment($styleID, $styleAttributes);

                        break;
                    case 'Borders':
                        $this->parseStyleBorders($styleID, $styleData, $namespaces);

                        break;
                    case 'Font':
                        $this->parseStyleFont($styleID, $styleAttributes);

                        break;
                    case 'Interior':
                        $this->parseStyleInterior($styleID, $styleAttributes);

                        break;
                    case 'NumberFormat':
                        $this->parseStyleNumberFormat($styleID, $styleAttributes);

                        break;
                }
            }
        }

        return $this->styles;
    }

    /**
     * @param string $styleID
     */
    private function parseStyleAlignment($styleID, SimpleXMLElement $styleAttributes): void
    {
        $verticalAlignmentStyles = [
            Alignment::VERTICAL_BOTTOM,
            Alignment::VERTICAL_TOP,
            Alignment::VERTICAL_CENTER,
            Alignment::VERTICAL_JUSTIFY,
        ];
        $horizontalAlignmentStyles = [
            Alignment::HORIZONTAL_GENERAL,
            Alignment::HORIZONTAL_LEFT,
            Alignment::HORIZONTAL_RIGHT,
            Alignment::HORIZONTAL_CENTER,
            Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            Alignment::HORIZONTAL_JUSTIFY,
        ];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'Vertical':
                    if (self::identifyFixedStyleValue($verticalAlignmentStyles, $styleAttributeValue)) {
                        $this->styles[$styleID]['alignment']['vertical'] = $styleAttributeValue;
                    }

                    break;
                case 'Horizontal':
                    if (self::identifyFixedStyleValue($horizontalAlignmentStyles, $styleAttributeValue)) {
                        $this->styles[$styleID]['alignment']['horizontal'] = $styleAttributeValue;
                    }

                    break;
                case 'WrapText':
                    $this->styles[$styleID]['alignment']['wrapText'] = true;

                    break;
                case 'Rotate':
                    $this->styles[$styleID]['alignment']['textRotation'] = $styleAttributeValue;

                    break;
            }
        }
    }

    private static $borderPositions = ['top', 'left', 'bottom', 'right'];

    private function parseStyleBorders($styleID, SimpleXMLElement $styleData, array $namespaces): void
    {
        $diagonalDirection = '';
        $borderPosition = '';
        foreach ($styleData->Border as $borderStyle) {
            $borderAttributes = self::getAttributes($borderStyle, $namespaces['ss']);
            $thisBorder = [];
            $style = (string) $borderAttributes->Weight;
            $style .= strtolower((string) $borderAttributes->LineStyle);
            $thisBorder['borderStyle'] = self::$mappings['borderStyle'][$style] ?? Border::BORDER_NONE;
            foreach ($borderAttributes as $borderStyleKey => $borderStyleValuex) {
                $borderStyleValue = (string) $borderStyleValuex;
                switch ($borderStyleKey) {
                    case 'Position':
                        $borderStyleValue = strtolower($borderStyleValue);
                        if (in_array($borderStyleValue, self::$borderPositions)) {
                            $borderPosition = $borderStyleValue;
                        } elseif ($borderStyleValue == 'diagonalleft') {
                            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_DOWN;
                        } elseif ($borderStyleValue == 'diagonalright') {
                            $diagonalDirection = $diagonalDirection ? Borders::DIAGONAL_BOTH : Borders::DIAGONAL_UP;
                        }

                        break;
                    case 'Color':
                        $borderColour = substr($borderStyleValue, 1);
                        $thisBorder['color']['rgb'] = $borderColour;

                        break;
                }
            }
            if ($borderPosition) {
                $this->styles[$styleID]['borders'][$borderPosition] = $thisBorder;
            } elseif ($diagonalDirection) {
                $this->styles[$styleID]['borders']['diagonalDirection'] = $diagonalDirection;
                $this->styles[$styleID]['borders']['diagonal'] = $thisBorder;
            }
        }
    }

    private static $underlineStyles = [
        Font::UNDERLINE_NONE,
        Font::UNDERLINE_DOUBLE,
        Font::UNDERLINE_DOUBLEACCOUNTING,
        Font::UNDERLINE_SINGLE,
        Font::UNDERLINE_SINGLEACCOUNTING,
    ];

    private function parseStyleFontUnderline(string $styleID, string $styleAttributeValue): void
    {
        if (self::identifyFixedStyleValue(self::$underlineStyles, $styleAttributeValue)) {
            $this->styles[$styleID]['font']['underline'] = $styleAttributeValue;
        }
    }

    private function parseStyleFontVerticalAlign(string $styleID, string $styleAttributeValue): void
    {
        if ($styleAttributeValue == 'Superscript') {
            $this->styles[$styleID]['font']['superscript'] = true;
        }
        if ($styleAttributeValue == 'Subscript') {
            $this->styles[$styleID]['font']['subscript'] = true;
        }
    }

    private function parseStyleFont(string $styleID, SimpleXMLElement $styleAttributes): void
    {
        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            switch ($styleAttributeKey) {
                case 'FontName':
                    $this->styles[$styleID]['font']['name'] = $styleAttributeValue;

                    break;
                case 'Size':
                    $this->styles[$styleID]['font']['size'] = $styleAttributeValue;

                    break;
                case 'Color':
                    $this->styles[$styleID]['font']['color']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Bold':
                    $this->styles[$styleID]['font']['bold'] = true;

                    break;
                case 'Italic':
                    $this->styles[$styleID]['font']['italic'] = true;

                    break;
                case 'Underline':
                    $this->parseStyleFontUnderline($styleID, $styleAttributeValue);

                    break;
                case 'VerticalAlign':
                    $this->parseStyleFontVerticalAlign($styleID, $styleAttributeValue);

                    break;
            }
        }
    }

    private function parseStyleInterior($styleID, SimpleXMLElement $styleAttributes): void
    {
        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValuex) {
            $styleAttributeValue = (string) $styleAttributeValuex;
            switch ($styleAttributeKey) {
                case 'Color':
                    $this->styles[$styleID]['fill']['endColor']['rgb'] = substr($styleAttributeValue, 1);
                    $this->styles[$styleID]['fill']['startColor']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'PatternColor':
                    $this->styles[$styleID]['fill']['startColor']['rgb'] = substr($styleAttributeValue, 1);

                    break;
                case 'Pattern':
                    $lcStyleAttributeValue = strtolower((string) $styleAttributeValue);
                    $this->styles[$styleID]['fill']['fillType']
                        = self::$mappings['fillType'][$lcStyleAttributeValue] ?? Fill::FILL_NONE;

                    break;
            }
        }
    }

    private function parseStyleNumberFormat($styleID, SimpleXMLElement $styleAttributes): void
    {
        $fromFormats = ['\-', '\ '];
        $toFormats = ['-', ' '];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = str_replace($fromFormats, $toFormats, $styleAttributeValue);
            switch ($styleAttributeValue) {
                case 'Short Date':
                    $styleAttributeValue = 'dd/mm/yyyy';

                    break;
            }

            if ($styleAttributeValue > '') {
                $this->styles[$styleID]['numberFormat']['formatCode'] = $styleAttributeValue;
            }
        }
    }

    private static function identifyFixedStyleValue($styleList, &$styleAttributeValue)
    {
        $returnValue = false;
        $styleAttributeValue = strtolower($styleAttributeValue);
        foreach ($styleList as $style) {
            if ($styleAttributeValue == strtolower($style)) {
                $styleAttributeValue = $style;
                $returnValue = true;

                break;
            }
        }

        return $returnValue;
    }

    private static function getAttributes(?SimpleXMLElement $simple, string $node): SimpleXMLElement
    {
        return ($simple === null)
            ? new SimpleXMLElement('<xml></xml>')
            : ($simple->attributes($node) ?? new SimpleXMLElement('<xml></xml>'));
    }
}
