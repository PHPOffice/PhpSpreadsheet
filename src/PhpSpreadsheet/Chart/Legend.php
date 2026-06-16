<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class Legend
{
    /** Legend positions */
    const XL_LEGEND_POSITION_BOTTOM = -4107; //    Below the chart.
    const XL_LEGEND_POSITION_CORNER = 2; //    In the upper right-hand corner of the chart border.
    const XL_LEGEND_POSITION_CUSTOM = -4161; //    A custom position.
    const XL_LEGEND_POSITION_LEFT = -4131; //    Left of the chart.
    const XL_LEGEND_POSITION_RIGHT = -4152; //    Right of the chart.
    const XL_LEGEND_POSITION_TOP = -4160; //    Above the chart.

    const POSITION_RIGHT = 'r';
    const POSITION_LEFT = 'l';
    const POSITION_BOTTOM = 'b';
    const POSITION_TOP = 't';
    const POSITION_TOPRIGHT = 'tr';

    const POSITION_XLREF = [
        self::XL_LEGEND_POSITION_BOTTOM => self::POSITION_BOTTOM,
        self::XL_LEGEND_POSITION_CORNER => self::POSITION_TOPRIGHT,
        self::XL_LEGEND_POSITION_CUSTOM => '??',
        self::XL_LEGEND_POSITION_LEFT => self::POSITION_LEFT,
        self::XL_LEGEND_POSITION_RIGHT => self::POSITION_RIGHT,
        self::XL_LEGEND_POSITION_TOP => self::POSITION_TOP,
    ];

    /**
     * Legend position.
     */
    private string $position = self::POSITION_RIGHT;

    /**
     * Allow overlay of other elements?
     */
    private bool $overlay = true;

    /**
     * Legend Layout.
     */
    private ?Layout $layout;

    private GridLines $borderLines;

    private ChartColor $fillColor;

    private ?AxisText $legendText = null;

    /**
     * Create a new Legend.
     */
    public function __construct(string $position = self::POSITION_RIGHT, ?Layout $layout = null, bool $overlay = false)
    {
        $this->setPosition($position);
        $this->layout = $layout;
        $this->setOverlay($overlay);
        $this->borderLines = new GridLines();
        $this->fillColor = new ChartColor();
    }

    public function getFillColor(): ChartColor
    {
        return $this->fillColor;
    }

    /**
     * Get legend position as an Excel string value.
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * Get legend position using an Excel string value.
     *
     * @param string $position see self::POSITION_*
     */
    public function setPosition(string $position): bool
    {
        if (!in_array($position, self::POSITION_XLREF)) {
            return false;
        }

        $this->position = $position;

        return true;
    }

    /**
     * Get legend position as an Excel internal numeric value.
     */
    public function getPositionXL(): false|int
    {
        return array_search($this->position, self::POSITION_XLREF);
    }

    /**
     * Set legend position using an Excel internal numeric value.
     *
     * @param int $positionXL see self::XL_LEGEND_POSITION_*
     */
    public function setPositionXL(int $positionXL): bool
    {
        if (!isset(self::POSITION_XLREF[$positionXL])) {
            return false;
        }

        $this->position = self::POSITION_XLREF[$positionXL];

        return true;
    }

    /**
     * Get allow overlay of other elements?
     */
    public function getOverlay(): bool
    {
        return $this->overlay;
    }

    /**
     * Set allow overlay of other elements?
     */
    public function setOverlay(bool $overlay): void
    {
        $this->overlay = $overlay;
    }

    /**
     * Get Layout.
     */
    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    public function getLegendText(): ?AxisText
    {
        return $this->legendText;
    }

    public function setLegendText(?AxisText $legendText): self
    {
        $this->legendText = $legendText;

        return $this;
    }

    public function getBorderLines(): GridLines
    {
        return $this->borderLines;
    }

    public function setBorderLines(GridLines $borderLines): self
    {
        $this->borderLines = $borderLines;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $this->layout = ($this->layout === null) ? null : clone $this->layout;
        $this->legendText = ($this->legendText === null) ? null : clone $this->legendText;
        $this->borderLines = clone $this->borderLines;
        $this->fillColor = clone $this->fillColor;
    }
}
