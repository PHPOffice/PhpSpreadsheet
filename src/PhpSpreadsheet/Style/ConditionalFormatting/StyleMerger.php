<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;

class StyleMerger
{
    /**
     * @var Style
     */
    protected $baseStyle;

    public function __construct(Style $baseStyle)
    {
        $this->baseStyle = $baseStyle;
    }

    public function getStyle(): Style
    {
        return $this->baseStyle;
    }

    public function mergeStyle(Style $style): void
    {
        if ($style->getNumberFormat() !== null && $style->getNumberFormat()->getFormatCode() !== null) {
            $this->baseStyle->getNumberFormat()->setFormatCode($style->getNumberFormat()->getFormatCode());
        }

        if ($style->getFont() !== null) {
            $this->mergeFontStyle($this->baseStyle->getFont(), $style->getFont());
        }

        if ($style->getFill() !== null) {
            $this->mergeFillStyle($this->baseStyle->getFill(), $style->getFill());
        }

        if ($style->getBorders() !== null) {
            $this->mergeBordersStyle($this->baseStyle->getBorders(), $style->getBorders());
        }
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

        if ($fontStyle->getColor() !== null && $fontStyle->getColor()->getARGB() !== null) {
            $baseFontStyle->setColor($fontStyle->getColor());
        }
    }

    protected function mergeFillStyle(Fill $baseFillStyle, Fill $fillStyle): void
    {
        if ($fillStyle->getFillType() !== null) {
            $baseFillStyle->setFillType($fillStyle->getFillType());
        }

        //if ($fillStyle->getRotation() !== null) {
        $baseFillStyle->setRotation($fillStyle->getRotation());
        //}

        if ($fillStyle->getStartColor() !== null && $fillStyle->getStartColor()->getARGB() !== null) {
            $baseFillStyle->setStartColor($fillStyle->getStartColor());
        }

        if ($fillStyle->getEndColor() !== null && $fillStyle->getEndColor()->getARGB() !== null) {
            $baseFillStyle->setEndColor($fillStyle->getEndColor());
        }
    }

    protected function mergeBordersStyle(Borders $baseBordersStyle, Borders $bordersStyle): void
    {
        if ($bordersStyle->getTop() !== null) {
            $this->mergeBorderStyle($baseBordersStyle->getTop(), $bordersStyle->getTop());
        }

        if ($bordersStyle->getBottom() !== null) {
            $this->mergeBorderStyle($baseBordersStyle->getBottom(), $bordersStyle->getBottom());
        }

        if ($bordersStyle->getLeft() !== null) {
            $this->mergeBorderStyle($baseBordersStyle->getLeft(), $bordersStyle->getLeft());
        }

        if ($bordersStyle->getRight() !== null) {
            $this->mergeBorderStyle($baseBordersStyle->getRight(), $bordersStyle->getRight());
        }
    }

    protected function mergeBorderStyle(Border $baseBorderStyle, Border $borderStyle): void
    {
        //if ($borderStyle->getBorderStyle() !== null) {
        $baseBorderStyle->setBorderStyle($borderStyle->getBorderStyle());
        //}

        if ($borderStyle->getColor() !== null && $borderStyle->getColor()->getARGB() !== null) {
            $baseBorderStyle->setColor($borderStyle->getColor());
        }
    }
}
