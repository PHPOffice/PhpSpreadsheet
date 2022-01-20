<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
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
use stdClass;

class Styles extends BaseParserClass
{
    /**
     * Theme instance.
     *
     * @var ?Theme
     */
    private $theme;

    /** @var array */
    private $styles = [];

    /** @var array */
    private $cellStyles = [];

    /** @var SimpleXMLElement */
    private $styleXml;

    public function setStyleXml(SimpleXmlElement $styleXml): void
    {
        $this->styleXml = $styleXml;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    public function setStyleBaseData(?Theme $theme = null, array $styles = [], array $cellStyles = []): void
    {
        $this->theme = $theme;
        $this->styles = $styles;
        $this->cellStyles = $cellStyles;
    }

    public function readFontStyle(Font $fontStyle, SimpleXMLElement $fontStyleXml): void
    {
        if (isset($fontStyleXml->name, $fontStyleXml->name['val'])) {
            $fontStyle->setName((string) $fontStyleXml->name['val']);
        }
        if (isset($fontStyleXml->sz, $fontStyleXml->sz['val'])) {
            $fontStyle->setSize((float) $fontStyleXml->sz['val']);
        }
        if (isset($fontStyleXml->b)) {
            $fontStyle->setBold(!isset($fontStyleXml->b['val']) || self::boolean((string) $fontStyleXml->b['val']));
        }
        if (isset($fontStyleXml->i)) {
            $fontStyle->setItalic(!isset($fontStyleXml->i['val']) || self::boolean((string) $fontStyleXml->i['val']));
        }
        if (isset($fontStyleXml->strike)) {
            $fontStyle->setStrikethrough(
                !isset($fontStyleXml->strike['val']) || self::boolean((string) $fontStyleXml->strike['val'])
            );
        }
        $fontStyle->getColor()->setARGB($this->readColor($fontStyleXml->color));

        if (isset($fontStyleXml->u) && !isset($fontStyleXml->u['val'])) {
            $fontStyle->setUnderline(Font::UNDERLINE_SINGLE);
        } elseif (isset($fontStyleXml->u, $fontStyleXml->u['val'])) {
            $fontStyle->setUnderline((string) $fontStyleXml->u['val']);
        }

        if (isset($fontStyleXml->vertAlign, $fontStyleXml->vertAlign['val'])) {
            $verticalAlign = strtolower((string) $fontStyleXml->vertAlign['val']);
            if ($verticalAlign === 'superscript') {
                $fontStyle->setSuperscript(true);
            } elseif ($verticalAlign === 'subscript') {
                $fontStyle->setSubscript(true);
            }
        }
    }

    private function readNumberFormat(NumberFormat $numfmtStyle, SimpleXMLElement $numfmtStyleXml): void
    {
        if ($numfmtStyleXml->count() === 0) {
            return;
        }
        $numfmt = Xlsx::getAttributes($numfmtStyleXml);
        if ($numfmt->count() > 0 && isset($numfmt['formatCode'])) {
            $numfmtStyle->setFormatCode(self::formatGeneral((string) $numfmt['formatCode']));
        }
    }

    public function readFillStyle(Fill $fillStyle, SimpleXMLElement $fillStyleXml): void
    {
        if ($fillStyleXml->gradientFill) {
            /** @var SimpleXMLElement $gradientFill */
            $gradientFill = $fillStyleXml->gradientFill[0];
            if (!empty($gradientFill['type'])) {
                $fillStyle->setFillType((string) $gradientFill['type']);
            }
            $fillStyle->setRotation((float) ($gradientFill['degree']));
            $gradientFill->registerXPathNamespace('sml', Namespaces::MAIN);
            $fillStyle->getStartColor()->setARGB($this->readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=0]'))->color));
            $fillStyle->getEndColor()->setARGB($this->readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=1]'))->color));
        } elseif ($fillStyleXml->patternFill) {
            $defaultFillStyle = Fill::FILL_NONE;
            if ($fillStyleXml->patternFill->fgColor) {
                $fillStyle->getStartColor()->setARGB($this->readColor($fillStyleXml->patternFill->fgColor, true));
                $defaultFillStyle = Fill::FILL_SOLID;
            }
            if ($fillStyleXml->patternFill->bgColor) {
                $fillStyle->getEndColor()->setARGB($this->readColor($fillStyleXml->patternFill->bgColor, true));
                $defaultFillStyle = Fill::FILL_SOLID;
            }

            $patternType = (string) $fillStyleXml->patternFill['patternType'] != ''
                ? (string) $fillStyleXml->patternFill['patternType']
                : $defaultFillStyle;

            $fillStyle->setFillType($patternType);
        }
    }

    public function readBorderStyle(Borders $borderStyle, SimpleXMLElement $borderStyleXml): void
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

        $this->readBorder($borderStyle->getLeft(), $borderStyleXml->left);
        $this->readBorder($borderStyle->getRight(), $borderStyleXml->right);
        $this->readBorder($borderStyle->getTop(), $borderStyleXml->top);
        $this->readBorder($borderStyle->getBottom(), $borderStyleXml->bottom);
        $this->readBorder($borderStyle->getDiagonal(), $borderStyleXml->diagonal);
    }

    private function readBorder(Border $border, SimpleXMLElement $borderXml): void
    {
        if (isset($borderXml['style'])) {
            $border->setBorderStyle((string) $borderXml['style']);
        }
        if (isset($borderXml->color)) {
            $border->getColor()->setARGB($this->readColor($borderXml->color));
        }
    }

    public function readAlignmentStyle(Alignment $alignment, SimpleXMLElement $alignmentXml): void
    {
        $alignment->setHorizontal((string) $alignmentXml['horizontal']);
        $alignment->setVertical((string) $alignmentXml['vertical']);

        $textRotation = 0;
        if ((int) $alignmentXml['textRotation'] <= 90) {
            $textRotation = (int) $alignmentXml['textRotation'];
        } elseif ((int) $alignmentXml['textRotation'] > 90) {
            $textRotation = 90 - (int) $alignmentXml['textRotation'];
        }

        $alignment->setTextRotation((int) $textRotation);
        $alignment->setWrapText(self::boolean((string) $alignmentXml['wrapText']));
        $alignment->setShrinkToFit(self::boolean((string) $alignmentXml['shrinkToFit']));
        $alignment->setIndent(
            (int) ((string) $alignmentXml['indent']) > 0 ? (int) ((string) $alignmentXml['indent']) : 0
        );
        $alignment->setReadOrder(
            (int) ((string) $alignmentXml['readingOrder']) > 0 ? (int) ((string) $alignmentXml['readingOrder']) : 0
        );
    }

    private static function formatGeneral(string $formatString): string
    {
        if ($formatString === 'GENERAL') {
            $formatString = NumberFormat::FORMAT_GENERAL;
        }

        return $formatString;
    }

    /**
     * Read style.
     *
     * @param SimpleXMLElement|stdClass $style
     */
    public function readStyle(Style $docStyle, $style): void
    {
        if ($style->numFmt instanceof SimpleXMLElement) {
            $this->readNumberFormat($docStyle->getNumberFormat(), $style->numFmt);
        } else {
            $docStyle->getNumberFormat()->setFormatCode(self::formatGeneral((string) $style->numFmt));
        }

        if (isset($style->font)) {
            $this->readFontStyle($docStyle->getFont(), $style->font);
        }

        if (isset($style->fill)) {
            $this->readFillStyle($docStyle->getFill(), $style->fill);
        }

        if (isset($style->border)) {
            $this->readBorderStyle($docStyle->getBorders(), $style->border);
        }

        if (isset($style->alignment)) {
            $this->readAlignmentStyle($docStyle->getAlignment(), $style->alignment);
        }

        // protection
        if (isset($style->protection)) {
            $this->readProtectionLocked($docStyle, $style);
            $this->readProtectionHidden($docStyle, $style);
        }

        // top-level style settings
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix((bool) $style->quotePrefix);
        }
    }

    /**
     * Read protection locked attribute.
     *
     * @param SimpleXMLElement|stdClass $style
     */
    public function readProtectionLocked(Style $docStyle, $style): void
    {
        if (isset($style->protection['locked'])) {
            if (self::boolean((string) $style->protection['locked'])) {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    /**
     * Read protection hidden attribute.
     *
     * @param SimpleXMLElement|stdClass $style
     */
    public function readProtectionHidden(Style $docStyle, $style): void
    {
        if (isset($style->protection['hidden'])) {
            if (self::boolean((string) $style->protection['hidden'])) {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    public function readColor(SimpleXMLElement $color, bool $background = false): string
    {
        if (isset($color['rgb'])) {
            return (string) $color['rgb'];
        } elseif (isset($color['indexed'])) {
            return Color::indexedColor((int) ($color['indexed'] - 7), $background)->getARGB() ?? '';
        } elseif (isset($color['theme'])) {
            if ($this->theme !== null) {
                $returnColour = $this->theme->getColourByIndex((int) $color['theme']);
                if (isset($color['tint'])) {
                    $tintAdjust = (float) $color['tint'];
                    $returnColour = Color::changeBrightness($returnColour ?? '', $tintAdjust);
                }

                return 'FF' . $returnColour;
            }
        }

        return ($background) ? 'FFFFFFFF' : 'FF000000';
    }

    public function dxfs(bool $readDataOnly = false): array
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
                foreach ($this->styleXml->cellStyles->cellStyle as $cellStylex) {
                    $cellStyle = Xlsx::getAttributes($cellStylex);
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

    public function styles(): array
    {
        return $this->styles;
    }

    /**
     * Get array item.
     *
     * @param mixed $array (usually array, in theory can be false)
     *
     * @return stdClass
     */
    private static function getArrayItem($array, int $key = 0)
    {
        return is_array($array) ? ($array[$key] ?? null) : null;
    }
}
