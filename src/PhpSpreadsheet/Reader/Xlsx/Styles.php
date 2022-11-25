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
    private $workbookPalette = [];

    /** @var array */
    private $styles = [];

    /** @var array */
    private $cellStyles = [];

    /** @var SimpleXMLElement */
    private $styleXml;

    /** @var string */
    private $namespace = '';

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function setWorkbookPalette(array $palette): void
    {
        $this->workbookPalette = $palette;
    }

    /**
     * Cast SimpleXMLElement to bool to overcome Scrutinizer problem.
     *
     * @param mixed $value
     */
    private static function castBool($value): bool
    {
        return (bool) $value;
    }

    private function getStyleAttributes(SimpleXMLElement $value): SimpleXMLElement
    {
        $attr = null;
        if (self::castBool($value)) {
            $attr = $value->attributes('');
            if ($attr === null || count($attr) === 0) {
                $attr = $value->attributes($this->namespace);
            }
        }

        return Xlsx::testSimpleXml($attr);
    }

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
        if (isset($fontStyleXml->name)) {
            $attr = $this->getStyleAttributes($fontStyleXml->name);
            if (isset($attr['val'])) {
                $fontStyle->setName((string) $attr['val']);
            }
        }
        if (isset($fontStyleXml->sz)) {
            $attr = $this->getStyleAttributes($fontStyleXml->sz);
            if (isset($attr['val'])) {
                $fontStyle->setSize((float) $attr['val']);
            }
        }
        if (isset($fontStyleXml->b)) {
            $attr = $this->getStyleAttributes($fontStyleXml->b);
            $fontStyle->setBold(!isset($attr['val']) || self::boolean((string) $attr['val']));
        }
        if (isset($fontStyleXml->i)) {
            $attr = $this->getStyleAttributes($fontStyleXml->i);
            $fontStyle->setItalic(!isset($attr['val']) || self::boolean((string) $attr['val']));
        }
        if (isset($fontStyleXml->strike)) {
            $attr = $this->getStyleAttributes($fontStyleXml->strike);
            $fontStyle->setStrikethrough(!isset($attr['val']) || self::boolean((string) $attr['val']));
        }
        $fontStyle->getColor()->setARGB($this->readColor($fontStyleXml->color));

        if (isset($fontStyleXml->u)) {
            $attr = $this->getStyleAttributes($fontStyleXml->u);
            if (!isset($attr['val'])) {
                $fontStyle->setUnderline(Font::UNDERLINE_SINGLE);
            } else {
                $fontStyle->setUnderline((string) $attr['val']);
            }
        }
        if (isset($fontStyleXml->vertAlign)) {
            $attr = $this->getStyleAttributes($fontStyleXml->vertAlign);
            if (isset($attr['val'])) {
                $verticalAlign = strtolower((string) $attr['val']);
                if ($verticalAlign === 'superscript') {
                    $fontStyle->setSuperscript(true);
                } elseif ($verticalAlign === 'subscript') {
                    $fontStyle->setSubscript(true);
                }
            }
        }
    }

    private function readNumberFormat(NumberFormat $numfmtStyle, SimpleXMLElement $numfmtStyleXml): void
    {
        if ((string) $numfmtStyleXml['formatCode'] !== '') {
            $numfmtStyle->setFormatCode(self::formatGeneral((string) $numfmtStyleXml['formatCode']));

            return;
        }
        $numfmt = $this->getStyleAttributes($numfmtStyleXml);
        if (isset($numfmt['formatCode'])) {
            $numfmtStyle->setFormatCode(self::formatGeneral((string) $numfmt['formatCode']));
        }
    }

    public function readFillStyle(Fill $fillStyle, SimpleXMLElement $fillStyleXml): void
    {
        if ($fillStyleXml->gradientFill) {
            /** @var SimpleXMLElement $gradientFill */
            $gradientFill = $fillStyleXml->gradientFill[0];
            $attr = $this->getStyleAttributes($gradientFill);
            if (!empty($attr['type'])) {
                $fillStyle->setFillType((string) $attr['type']);
            }
            $fillStyle->setRotation((float) ($attr['degree']));
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

            $type = '';
            if ((string) $fillStyleXml->patternFill['patternType'] !== '') {
                $type = (string) $fillStyleXml->patternFill['patternType'];
            } else {
                $attr = $this->getStyleAttributes($fillStyleXml->patternFill);
                $type = (string) $attr['patternType'];
            }
            $patternType = ($type === '') ? $defaultFillStyle : $type;

            $fillStyle->setFillType($patternType);
        }
    }

    public function readBorderStyle(Borders $borderStyle, SimpleXMLElement $borderStyleXml): void
    {
        $diagonalUp = $this->getAttribute($borderStyleXml, 'diagonalUp');
        $diagonalUp = self::boolean($diagonalUp);
        $diagonalDown = $this->getAttribute($borderStyleXml, 'diagonalDown');
        $diagonalDown = self::boolean($diagonalDown);
        if ($diagonalUp === false) {
            if ($diagonalDown === false) {
                $borderStyle->setDiagonalDirection(Borders::DIAGONAL_NONE);
            } else {
                $borderStyle->setDiagonalDirection(Borders::DIAGONAL_DOWN);
            }
        } elseif ($diagonalDown === false) {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_UP);
        } else {
            $borderStyle->setDiagonalDirection(Borders::DIAGONAL_BOTH);
        }

        $this->readBorder($borderStyle->getLeft(), $borderStyleXml->left);
        $this->readBorder($borderStyle->getRight(), $borderStyleXml->right);
        $this->readBorder($borderStyle->getTop(), $borderStyleXml->top);
        $this->readBorder($borderStyle->getBottom(), $borderStyleXml->bottom);
        $this->readBorder($borderStyle->getDiagonal(), $borderStyleXml->diagonal);
    }

    private function getAttribute(SimpleXMLElement $xml, string $attribute): string
    {
        $style = '';
        if ((string) $xml[$attribute] !== '') {
            $style = (string) $xml[$attribute];
        } else {
            $attr = $this->getStyleAttributes($xml);
            if (isset($attr[$attribute])) {
                $style = (string) $attr[$attribute];
            }
        }

        return $style;
    }

    private function readBorder(Border $border, SimpleXMLElement $borderXml): void
    {
        $style = $this->getAttribute($borderXml, 'style');
        if ($style !== '') {
            $border->setBorderStyle((string) $style);
        }
        if (isset($borderXml->color)) {
            $border->getColor()->setARGB($this->readColor($borderXml->color));
        }
    }

    public function readAlignmentStyle(Alignment $alignment, SimpleXMLElement $alignmentXml): void
    {
        $horizontal = $this->getAttribute($alignmentXml, 'horizontal');
        $alignment->setHorizontal($horizontal);
        $vertical = $this->getAttribute($alignmentXml, 'vertical');
        $alignment->setVertical((string) $vertical);

        $textRotation = (int) $this->getAttribute($alignmentXml, 'textRotation');
        if ($textRotation > 90) {
            $textRotation = 90 - $textRotation;
        }
        $alignment->setTextRotation($textRotation);

        $wrapText = $this->getAttribute($alignmentXml, 'wrapText');
        $alignment->setWrapText(self::boolean((string) $wrapText));
        $shrinkToFit = $this->getAttribute($alignmentXml, 'shrinkToFit');
        $alignment->setShrinkToFit(self::boolean((string) $shrinkToFit));
        $indent = (int) $this->getAttribute($alignmentXml, 'indent');
        $alignment->setIndent(max($indent, 0));
        $readingOrder = (int) $this->getAttribute($alignmentXml, 'readingOrder');
        $alignment->setReadOrder(max($readingOrder, 0));
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
        if ($style instanceof SimpleXMLElement) {
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
            $this->readProtectionLocked($docStyle, $style->protection);
            $this->readProtectionHidden($docStyle, $style->protection);
        }

        // top-level style settings
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix((bool) $style->quotePrefix);
        }
    }

    /**
     * Read protection locked attribute.
     */
    public function readProtectionLocked(Style $docStyle, SimpleXMLElement $style): void
    {
        $locked = '';
        if ((string) $style['locked'] !== '') {
            $locked = (string) $style['locked'];
        } else {
            $attr = $this->getStyleAttributes($style);
            if (isset($attr['locked'])) {
                $locked = (string) $attr['locked'];
            }
        }
        if ($locked !== '') {
            if (self::boolean($locked)) {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    /**
     * Read protection hidden attribute.
     */
    public function readProtectionHidden(Style $docStyle, SimpleXMLElement $style): void
    {
        $hidden = '';
        if ((string) $style['hidden'] !== '') {
            $hidden = (string) $style['hidden'];
        } else {
            $attr = $this->getStyleAttributes($style);
            if (isset($attr['hidden'])) {
                $hidden = (string) $attr['hidden'];
            }
        }
        if ($hidden !== '') {
            if (self::boolean((string) $hidden)) {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_PROTECTED);
            } else {
                $docStyle->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    public function readColor(SimpleXMLElement $color, bool $background = false): string
    {
        $attr = $this->getStyleAttributes($color);
        if (isset($attr['rgb'])) {
            return (string) $attr['rgb'];
        }
        if (isset($attr['indexed'])) {
            $indexedColor = (int) $attr['indexed'];
            if ($indexedColor >= count($this->workbookPalette)) {
                return Color::indexedColor($indexedColor - 7, $background)->getARGB() ?? '';
            }

            return Color::indexedColor($indexedColor, $background, $this->workbookPalette)->getARGB() ?? '';
        }
        if (isset($attr['theme'])) {
            if ($this->theme !== null) {
                $returnColour = $this->theme->getColourByIndex((int) $attr['theme']);
                if (isset($attr['tint'])) {
                    $tintAdjust = (float) $attr['tint'];
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
