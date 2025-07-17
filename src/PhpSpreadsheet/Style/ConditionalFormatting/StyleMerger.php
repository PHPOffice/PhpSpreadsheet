<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;

class StyleMerger
{
    protected Style $baseStyle;

    public function __construct(Style $baseStyle)
    {
        // Setting to $baseStyle sometimes causes problems later on.
        $array = $baseStyle->exportArray();
        $this->baseStyle = new Style();
        $this->baseStyle->applyFromArray($array);
    }

    public function getStyle(): Style
    {
        return $this->baseStyle;
    }

    public function mergeStyle(Style $style): void
    {
        if ($style->getNumberFormat()->getFormatCode() !== null) {
            $this->baseStyle->getNumberFormat()->setFormatCode($style->getNumberFormat()->getFormatCode());
        }
        $this->mergeFontStyle($this->baseStyle->getFont(), $style->getFont());
        $this->mergeFillStyle($this->baseStyle->getFill(), $style->getFill());
        $this->mergeBordersStyle($this->baseStyle->getBorders(), $style->getBorders());
    }

    protected function mergeFontStyle(Font $baseFontStyle, Font $fontStyle): void
    {
        if ($fontStyle->getBold() !== null) {
            $baseFontStyle->setBold($fontStyle->getBold());
        }
        if ($fontStyle->getItalic() !== null) {
            $baseFontStyle->setItalic($fontStyle->getItalic());
        }
        if ($fontStyle->getStrikethrough() !== null) {
            $baseFontStyle->setStrikethrough($fontStyle->getStrikethrough());
        }
        if ($fontStyle->getUnderline() !== null) {
            $baseFontStyle->setUnderline($fontStyle->getUnderline());
        }
        if ($fontStyle->getColor()->getARGB() !== null) {
            $baseFontStyle->setColor($fontStyle->getColor());
        }
    }

    protected function mergeFillStyle(Fill $baseFillStyle, Fill $fillStyle): void
    {
        if ($fillStyle->getFillType() !== null) {
            $baseFillStyle->setFillType($fillStyle->getFillType());
        }
        $baseFillStyle->setRotation($fillStyle->getRotation());
        if ($fillStyle->getStartColor()->getARGB() !== null) {
            $baseFillStyle->setStartColor($fillStyle->getStartColor());
        }
        if ($fillStyle->getEndColor()->getARGB() !== null) {
            $baseFillStyle->setEndColor($fillStyle->getEndColor());
        }
    }

    protected function mergeBordersStyle(Borders $baseBordersStyle, Borders $bordersStyle): void
    {
        $this->mergeBorderStyle($baseBordersStyle->getTop(), $bordersStyle->getTop());
        $this->mergeBorderStyle($baseBordersStyle->getBottom(), $bordersStyle->getBottom());
        $this->mergeBorderStyle($baseBordersStyle->getLeft(), $bordersStyle->getLeft());
        $this->mergeBorderStyle($baseBordersStyle->getRight(), $bordersStyle->getRight());
    }

    protected function mergeBorderStyle(Border $baseBorderStyle, Border $borderStyle): void
    {
        if ($borderStyle->getBorderStyle() !== Border::BORDER_OMIT) {
            $baseBorderStyle->setBorderStyle(
                $borderStyle->getBorderStyle()
            );
        }
        if ($borderStyle->getColor()->getARGB() !== null) {
            $baseBorderStyle->setColor($borderStyle->getColor());
        }
    }
}
