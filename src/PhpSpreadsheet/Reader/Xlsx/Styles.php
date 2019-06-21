<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
    private $numFmts = null;

    private $styleXml;

    private $worksheet;

    public function __construct(\SimpleXMLElement $styleXml, Worksheet $workSheet)
    {
        $this->styleXml = $styleXml;
        $this->worksheet = $workSheet;
    }

    private static function readStyle(Style $docStyle, \SimpleXMLElement $style)
    {
        $docStyle->getNumberFormat()->setFormatCode($style->numFmt);

        // font
        if (isset($style->font)) {
            $docStyle->getFont()->setName((string) $style->font->name['val']);
            $docStyle->getFont()->setSize((string) $style->font->sz['val']);
            if (isset($style->font->b)) {
                $docStyle->getFont()->setBold(!isset($style->font->b['val']) || self::boolean((string) $style->font->b['val']));
            }
            if (isset($style->font->i)) {
                $docStyle->getFont()->setItalic(!isset($style->font->i['val']) || self::boolean((string) $style->font->i['val']));
            }
            if (isset($style->font->strike)) {
                $docStyle->getFont()->setStrikethrough(!isset($style->font->strike['val']) || self::boolean((string) $style->font->strike['val']));
            }
            $docStyle->getFont()->getColor()->setARGB(self::readColor($style->font->color));

            if (isset($style->font->u) && !isset($style->font->u['val'])) {
                $docStyle->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
            } elseif (isset($style->font->u, $style->font->u['val'])) {
                $docStyle->getFont()->setUnderline((string) $style->font->u['val']);
            }

            if (isset($style->font->vertAlign, $style->font->vertAlign['val'])) {
                $vertAlign = strtolower((string) $style->font->vertAlign['val']);
                if ($vertAlign == 'superscript') {
                    $docStyle->getFont()->setSuperscript(true);
                }
                if ($vertAlign == 'subscript') {
                    $docStyle->getFont()->setSubscript(true);
                }
            }
        }

        // fill
        if (isset($style->fill)) {
            if ($style->fill->gradientFill) {
                /** @var SimpleXMLElement $gradientFill */
                $gradientFill = $style->fill->gradientFill[0];
                if (!empty($gradientFill['type'])) {
                    $docStyle->getFill()->setFillType((string) $gradientFill['type']);
                }
                $docStyle->getFill()->setRotation((float) ($gradientFill['degree']));
                $gradientFill->registerXPathNamespace('sml', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $docStyle->getFill()->getStartColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=0]'))->color));
                $docStyle->getFill()->getEndColor()->setARGB(self::readColor(self::getArrayItem($gradientFill->xpath('sml:stop[@position=1]'))->color));
            } elseif ($style->fill->patternFill) {
                $patternType = (string) $style->fill->patternFill['patternType'] != '' ? (string) $style->fill->patternFill['patternType'] : 'solid';
                $docStyle->getFill()->setFillType($patternType);
                if ($style->fill->patternFill->fgColor) {
                    $docStyle->getFill()->getStartColor()->setARGB(self::readColor($style->fill->patternFill->fgColor, true));
                } else {
                    $docStyle->getFill()->getStartColor()->setARGB('FF000000');
                }
                if ($style->fill->patternFill->bgColor) {
                    $docStyle->getFill()->getEndColor()->setARGB(self::readColor($style->fill->patternFill->bgColor, true));
                }
            }
        }

        // border
        if (isset($style->border)) {
            $diagonalUp = self::boolean((string) $style->border['diagonalUp']);
            $diagonalDown = self::boolean((string) $style->border['diagonalDown']);
            if (!$diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_NONE);
            } elseif ($diagonalUp && !$diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_UP);
            } elseif (!$diagonalUp && $diagonalDown) {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_DOWN);
            } else {
                $docStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_BOTH);
            }
            self::readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
            self::readBorder($docStyle->getBorders()->getRight(), $style->border->right);
            self::readBorder($docStyle->getBorders()->getTop(), $style->border->top);
            self::readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
            self::readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
        }

        // alignment
        if (isset($style->alignment)) {
            $docStyle->getAlignment()->setHorizontal((string) $style->alignment['horizontal']);
            $docStyle->getAlignment()->setVertical((string) $style->alignment['vertical']);

            $textRotation = 0;
            if ((int) $style->alignment['textRotation'] <= 90) {
                $textRotation = (int) $style->alignment['textRotation'];
            } elseif ((int) $style->alignment['textRotation'] > 90) {
                $textRotation = 90 - (int) $style->alignment['textRotation'];
            }

            $docStyle->getAlignment()->setTextRotation((int) $textRotation);
            $docStyle->getAlignment()->setWrapText(self::boolean((string) $style->alignment['wrapText']));
            $docStyle->getAlignment()->setShrinkToFit(self::boolean((string) $style->alignment['shrinkToFit']));
            $docStyle->getAlignment()->setIndent((int) ((string) $style->alignment['indent']) > 0 ? (int) ((string) $style->alignment['indent']) : 0);
            $docStyle->getAlignment()->setReadOrder((int) ((string) $style->alignment['readingOrder']) > 0 ? (int) ((string) $style->alignment['readingOrder']) : 0);
        }

        // protection
        if (isset($style->protection)) {
            if (isset($style->protection['locked'])) {
                if (self::boolean((string) $style->protection['locked'])) {
                    $docStyle->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                }
            }

            if (isset($style->protection['hidden'])) {
                if (self::boolean((string) $style->protection['hidden'])) {
                    $docStyle->getProtection()->setHidden(Protection::PROTECTION_PROTECTED);
                } else {
                    $docStyle->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
                }
            }
        }

        // top-level style settings
        if (isset($style->quotePrefix)) {
            $docStyle->setQuotePrefix($style->quotePrefix);
        }
    }

    private static function readBorder(Border $docBorder, $eleBorder)
    {
        if (isset($eleBorder['style'])) {
            $docBorder->setBorderStyle((string) $eleBorder['style']);
        }
        if (isset($eleBorder->color)) {
            $docBorder->getColor()->setARGB(self::readColor($eleBorder->color));
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

        if ($background) {
            return 'FFFFFFFF';
        }

        return 'FF000000';
    }

    public function dfxs($readDataOnly = false)
    {
        $dxfs = [];
        if (!$readDataOnly && $this->styleXml) {
            //    Conditional Styles
            if ($this->styleXml->dxfs) {
                foreach ($this->styleXml->dxfs->dxf as $dxf) {
                    $style = new Style(false, true);
                    self::readStyle($style, $dxf);
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
                            self::readStyle($style, $this->cellStyles[(int) ($cellStyle['xfId'])]);

                            // normal style, currently not using it for anything
                        }
                    }
                }
            }
        }

        return $dxfs;
    }

    private static function getArrayItem($array, $key = 0)
    {
        return isset($array[$key]) ? $array[$key] : null;
    }
}
