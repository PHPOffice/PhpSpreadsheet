<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class AxisText extends Properties
{
    /** @var ?int */
    private $rotation;

    /** @var ChartColor */
    private $fillColor;

    public function __construct()
    {
        parent::__construct();
        $this->fillColor = new ChartColor();
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
        return $this->fillColor;
    }
}
