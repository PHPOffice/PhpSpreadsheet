<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Style\Font;

class AxisText extends Properties
{
    private ?int $rotation = null;

    private Font $font;

    public function __construct()
    {
        parent::__construct();
        $this->font = new Font();
        $this->font->setSize(null, true);
    }

    public function setRotation(?int $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getRotation(): ?int
    {
        return $this->rotation;
    }

    public function getFillColorObject(): ChartColor
    {
        $fillColor = $this->font->getChartColor();
        if ($fillColor === null) {
            $fillColor = new ChartColor();
            $this->font->setChartColorFromObject($fillColor);
        }

        return $fillColor;
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    public function setFont(Font $font): self
    {
        $this->font = $font;

        return $this;
    }
}
