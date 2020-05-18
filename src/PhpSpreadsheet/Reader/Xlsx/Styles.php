<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use SimpleXMLElement;

class Styles extends BaseParserClass
{
    /**
     * Theme instance.
     *
     * @var Theme
     */
    private static $theme = null;

    private $styles = [];

    private $cellStyles = [];

    private $styleXml;

    public function __construct(SimpleXMLElement $styleXml)
    {
        $this->styleXml = $styleXml;
    }

    public function setStyleBaseData(?Theme $theme = null, $styles = [], $cellStyles = []): void
    {
        self::$theme = $theme;
        $this->styles = $styles;
        $this->cellStyles = $cellStyles;
    }

    private static function readFontStyle(Font $fontStyle, SimpleXMLElement $fontStyleXml): void
    {
        $fontStyle->setName((string) $fontStyleXml->name['val']);
        $fontStyle->setSize((float) $fontStyleXml->sz['val']);

        if (isset($fontStyleXml->b)) {
            $fontStyle->setBold(!isset($fontStyleXml->b['val']) || self::boolean((string) $fontStyleXml->b['val']));
        }
        if (isset($fontStyleXml->i)) {
            $fontStyle->setItalic(!isset($fontStyleXml->i['val']) || self::boolean((string) $fontStyleXml->i['val']));
        }
        if (isset($fontStyleXml->strike)) {
            $fontStyle->setStrikethrough(!isset($fontStyleXml->strike['val']) || self::boolean((string) $fontStyleXml->strike['val']));
        }
        $fontStyle->getColor()->setARGB(self::readColor($fontStyleXml->color));

        if (isset($fontStyleXml->u) && !isset($fontStyleXml->u['val'])) {
            $fontStyle->setUnderline(Font::UNDERLINE_SINGLE);
        } elseif (isset($fontStyleXml->u, $fontStyleXml->u['val'])) {
            $fontStyle->setUnderline((string) $fontStyleXml->u['val']);
        }

        if (isset($fontStyleXml->vertAlign, $fontStyleXml->vertAlign['val'])) {
            $verticalAlign = strtolower((string) $fontStyleXml->vertAlign['val']);
            if ($verticalAlign === 'superscript') {
                $fontStyle->setSuperscript(true);
            }
            if ($verticalAlign === 'subscript') {
                $fontStyle->setSubscript(true);
            }
        }
    }

    private static function readNumberFormat(NumberFormat $numfmtStyle, SimpleXMLElement $numfmtStyleXml): void
    {
        if ($numfmtStyleXml->count() === 0) {
            return;
        }
        $numfmt = $numfmtStyleXml->attributes();
        if ($numfmt->count() > 0 && isset($numfmt['formatCode'])) {
            $numfmtStyle->setFormatCode((string) $numfmt['formatCode']);
        }
    }

    private static function readFillStyle(Fill $fillStyle, SimpleXMLElement $fillStyleXml): void
    {
        if ($fillStyleXml->gradientFill) {
            /** @var SimpleXMLElement $gradientFill */
            $gradientFill = $fillStyleXml->gradientFill[0];
            if (!empty($gradientFill['type'])) {
                $fillStyle->setFillType((string) $gradientFill['type']);
            }
            $fillStyle->setRotation((float) ($gradientFill['degree']));
            $gradientFill->registerXPathNamespace('sml', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $fillStyle->getStartColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=0]'))->color));
            $fillStyle->getEndColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=1]'))->color));
        } elseif ($fillStyleXml->patternFill) {
            $patternType = (string) $fillStyleXml->patternFill['patternType'] != '' ? (string) $fillStyleXml->patternFill['patternType'] : 'solid';
            $fillStyle->setFillType($patternType);
            if ($fillStyleXml->patternFill->fgColor) {
                $fillStyle->getStartColor()->setARGB(self::readColor($fillStyleXml->patternFill->fgColor, true));
            } else {
                $fillStyle->getStartColor()->setARGB('FF000000');
            }
            if ($fillStyleXml->patternFill->bgColor) {
                $fillStyle->getEndColor()->setARGB(self::readColor($fillStyleXml->patternFill->bgColor, true));
            }
        }
    }

    private static function readBorderStyle(Borders $borderStyle, SimpleXMLElement $borderStyleXml): void
    {
        $diagonalUp = self::boolean((string) $borderStyleXml['diagonalUp']);
        $diagonalDown = self::boolean((string) $borderStyleXml['diagonalDown']);
        if (!$diagonalUp && !$diagonalDown) {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_NONE);
        } elseif ($diagonalUp && !$diagonalDown) {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_UP);
        } elseif (!$diagonalUp && $diagonalDown) {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_DOWN);
        } else {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_BOTH);
        }

        self::readBorder($borderStyle->getLeft(), $borderStyleXml->left);
        self::readBorder($borderStyle->getRight(), $borderStyleXml->right);
        self::readBorder($borderStyle->getTop(), $borderStyleXml->top);
        self::readBorder($borderStyle->getBottom(), $borderStyleXml->bottom);
        self::readBorder($borderStyle->getDiagonal(), $borderStyleXml->diagonal);
    }

    private static function readBorder(Border $border, SimpleXMLElement $borderXml): void
    {
        if (isset($borderXml['style'])) {
            $border->setBorderStyle((string) $borderXml['style']);
        }
        if (isset($borderXml->color)) {
            $border->getColor()->setARGB(self::readColor($borderXml->color));
        }
    }

    private static function readAlignmentStyle(Alignment $alignment, SimpleXMLElement $alignmentXml): void
    {
        $alignment->setHorizontal((string) $alignmentXml->alignment['horizontal']);
        $alignment->setVertical((string) $alignmentXml->alignment['vertical']);

        $textRotation = 0;
        if ((int) $alignmentXml->alignment['textRotation'] <= 90) {
            $textRotation = (int) $alignmentXml->alignment['textRotation'];
        } elseif ((int) $alignmentXml->alignment['textRotation'] > 90) {
            $textRotation = 90 - (int) $alignmentXml->alignment['textRotation'];
        }

        $alignment->setTextRotation((int) $textRotation);
        $alignment->setWrapText(self::boolean((string) $alignmentXml->alignment['wrapText']));
        $alignment->setShrinkToFit(self::boolean((string) $alignmentXml->alignment['shrinkToFit']));
        $alignment->setIndent((int) ((string) $alignmentXml->alignment['indent']) > 0 ? (int) ((string) $alignmentXml->alignment['indent']) : 0);
        $alignment->setReadOrder((int) ((string) $alignmentXml->alignment['readingOrder']) > 0 ? (int) ((string) $alignmentXml->alignment['readingOrder']) : 0);
    }

    private function readStyle(Style $docStyle, $style): void
    {
        if ($style->numFmt instanceof SimpleXMLElement) {
            self::readNumberFormat($docStyle->getNumberFormat(), $style->numFmt);
        } else {
            $docStyle->getNumberFormat()->setFormatCode($style->numFmt);
        }

        if (isset($style->font)) {
            self::readFontStyle($docStyle->getFont(), $style->font);
        }

        if (isset($style->fill)) {
            self::readFillStyle($docStyle->getFill(), $style->fill);
        }

        if (isset($style->border)) {
            self::readBorderStyle($docStyle->getBorders(), $style->border);
        }

        if (isset($style->alignment->alignment)) {
            self::readAlignmentStyle($docStyle->getAlignment(), $style->alignment);
        }

        // protection
        if (isset($style->protection)) {
            $this->readProtectionLocked($docStyle, $style);
            $this->readProtectionHidden($docStyle, $style);
        }

        // top-level style settings
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix(true);
        }
    }

    private function readProtectionLocked(Style $docStyle, $style): void
    {
        if (isset($style->protection['locked'])) {
            if (self::boolean((string) $style->protection['locked'])) {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    private function readProtectionHidden(Style $docStyle, $style): void
    {
        if (isset($style->protection['hidden'])) {
            if (self::boolean((string) $style->protection['hidden'])) {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    private static function readColor($color, $background = false)
    {
        if (isset($color['rgb'])) {
            return (string) $color['rgb'];
        } elseif (isset($color['indexed'])) {
            return Color::indexedColor($color['indexed'] - 7, $background)->getARGB();
        } elseif (isset($color['theme'])) {
            if (self::$theme !== null) {
                $returnColour = self::$theme->getColourByIndex((int) $color['theme']);
                if (isset($color['tint'])) {
                    $tintAdjust = (float) $color['tint'];
                    $returnColour = Color::changeBrightness($returnColour, $tintAdjust);
                }

                return 'FF' . $returnColour;
            }
        }

        return ($background) ? 'FFFFFFFF' : 'FF000000';
    }

    public function dxfs($readDataOnly = false)
    {
        $dxfs = [];
        if (!$readDataOnly && $this->styleXml) {
            //    Conditional Styles
            if ($this->styleXml->dxfs) {
                foreach ($this->styleXml->dxfs->dxf as $dxf) {
                    $style = new Style(false, true);
                    $this->readStyle($style, $dxf);
                    $dxfs[] = $style;
                }
            }
            //    Cell Styles
            if ($this->styleXml->cellStyles) {
                foreach ($this->styleXml->cellStyles->cellStyle as $cellStyle) {
                    if ((int) ($cellStyle['builtinId']) == 0) {
                        if (isset($this->cellStyles[(int) ($cellStyle['xfId'])])) {
                            // Set default style
                            $style = new Style();
                            $this->readStyle($style, $this->cellStyles[(int) ($cellStyle['xfId'])]);

                            // normal style, currently not using it for anything
                        }
                    }
                }
            }
        }

        return $dxfs;
    }

    public function styles()
    {
        return $this->styles;
    }

    private static function getArrayItem($array, $key = 0)
    {
        return $array[$key] ?? null;
    }
}
