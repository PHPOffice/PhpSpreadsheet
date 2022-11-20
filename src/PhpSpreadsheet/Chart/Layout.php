<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class Layout
{
    /**
     * layoutTarget.
     *
     * @var ?string
     */
    private $layoutTarget;

    /**
     * X Mode.
     *
     * @var ?string
     */
    private $xMode;

    /**
     * Y Mode.
     *
     * @var ?string
     */
    private $yMode;

    /**
     * X-Position.
     *
     * @var ?float
     */
    private $xPos;

    /**
     * Y-Position.
     *
     * @var ?float
     */
    private $yPos;

    /**
     * width.
     *
     * @var ?float
     */
    private $width;

    /**
     * height.
     *
     * @var ?float
     */
    private $height;

    /**
     * Position - t=top.
     *
     * @var string
     */
    private $dLblPos = '';

    /** @var string */
    private $numFmtCode = '';

    /** @var bool */
    private $numFmtLinked = false;

    /**
     * show legend key
     * Specifies that legend keys should be shown in data labels.
     *
     * @var ?bool
     */
    private $showLegendKey;

    /**
     * show value
     * Specifies that the value should be shown in a data label.
     *
     * @var ?bool
     */
    private $showVal;

    /**
     * show category name
     * Specifies that the category name should be shown in the data label.
     *
     * @var ?bool
     */
    private $showCatName;

    /**
     * show data series name
     * Specifies that the series name should be shown in the data label.
     *
     * @var ?bool
     */
    private $showSerName;

    /**
     * show percentage
     * Specifies that the percentage should be shown in the data label.
     *
     * @var ?bool
     */
    private $showPercent;

    /**
     * show bubble size.
     *
     * @var ?bool
     */
    private $showBubbleSize;

    /**
     * show leader lines
     * Specifies that leader lines should be shown for the data label.
     *
     * @var ?bool
     */
    private $showLeaderLines;

    /** @var ?ChartColor */
    private $labelFillColor;

    /** @var ?ChartColor */
    private $labelBorderColor;

    /** @var ?ChartColor */
    private $labelFontColor;

    /**
     * Create a new Layout.
     */
    public function __construct(array $layout = [])
    {
        if (isset($layout['layoutTarget'])) {
            $this->layoutTarget = $layout['layoutTarget'];
        }
        if (isset($layout['xMode'])) {
            $this->xMode = $layout['xMode'];
        }
        if (isset($layout['yMode'])) {
            $this->yMode = $layout['yMode'];
        }
        if (isset($layout['x'])) {
            $this->xPos = (float) $layout['x'];
        }
        if (isset($layout['y'])) {
            $this->yPos = (float) $layout['y'];
        }
        if (isset($layout['w'])) {
            $this->width = (float) $layout['w'];
        }
        if (isset($layout['h'])) {
            $this->height = (float) $layout['h'];
        }
        if (isset($layout['dLblPos'])) {
            $this->dLblPos = (string) $layout['dLblPos'];
        }
        if (isset($layout['numFmtCode'])) {
            $this->numFmtCode = (string) $layout['numFmtCode'];
        }
        $this->initBoolean($layout, 'showLegendKey');
        $this->initBoolean($layout, 'showVal');
        $this->initBoolean($layout, 'showCatName');
        $this->initBoolean($layout, 'showSerName');
        $this->initBoolean($layout, 'showPercent');
        $this->initBoolean($layout, 'showBubbleSize');
        $this->initBoolean($layout, 'showLeaderLines');
        $this->initBoolean($layout, 'numFmtLinked');
        $this->initColor($layout, 'labelFillColor');
        $this->initColor($layout, 'labelBorderColor');
        $this->initColor($layout, 'labelFontColor');
    }

    private function initBoolean(array $layout, string $name): void
    {
        if (isset($layout[$name])) {
            $this->$name = (bool) $layout[$name];
        }
    }

    private function initColor(array $layout, string $name): void
    {
        if (isset($layout[$name]) && $layout[$name] instanceof ChartColor) {
            $this->$name = $layout[$name];
        }
    }

    /**
     * Get Layout Target.
     *
     * @return ?string
     */
    public function getLayoutTarget()
    {
        return $this->layoutTarget;
    }

    /**
     * Set Layout Target.
     *
     * @param ?string $target
     *
     * @return $this
     */
    public function setLayoutTarget($target)
    {
        $this->layoutTarget = $target;

        return $this;
    }

    /**
     * Get X-Mode.
     *
     * @return ?string
     */
    public function getXMode()
    {
        return $this->xMode;
    }

    /**
     * Set X-Mode.
     *
     * @param ?string $mode
     *
     * @return $this
     */
    public function setXMode($mode)
    {
        $this->xMode = (string) $mode;

        return $this;
    }

    /**
     * Get Y-Mode.
     *
     * @return ?string
     */
    public function getYMode()
    {
        return $this->yMode;
    }

    /**
     * Set Y-Mode.
     *
     * @param ?string $mode
     *
     * @return $this
     */
    public function setYMode($mode)
    {
        $this->yMode = (string) $mode;

        return $this;
    }

    /**
     * Get X-Position.
     *
     * @return null|float|int
     */
    public function getXPosition()
    {
        return $this->xPos;
    }

    /**
     * Set X-Position.
     *
     * @param ?float $position
     *
     * @return $this
     */
    public function setXPosition($position)
    {
        $this->xPos = (float) $position;

        return $this;
    }

    /**
     * Get Y-Position.
     *
     * @return null|float
     */
    public function getYPosition()
    {
        return $this->yPos;
    }

    /**
     * Set Y-Position.
     *
     * @param ?float $position
     *
     * @return $this
     */
    public function setYPosition($position)
    {
        $this->yPos = (float) $position;

        return $this;
    }

    /**
     * Get Width.
     *
     * @return ?float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width.
     *
     * @param ?float $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get Height.
     *
     * @return null|float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height.
     *
     * @param ?float $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    public function getShowLegendKey(): ?bool
    {
        return $this->showLegendKey;
    }

    /**
     * Set show legend key
     * Specifies that legend keys should be shown in data labels.
     */
    public function setShowLegendKey(?bool $showLegendKey): self
    {
        $this->showLegendKey = $showLegendKey;

        return $this;
    }

    public function getShowVal(): ?bool
    {
        return $this->showVal;
    }

    /**
     * Set show val
     * Specifies that the value should be shown in data labels.
     */
    public function setShowVal(?bool $showDataLabelValues): self
    {
        $this->showVal = $showDataLabelValues;

        return $this;
    }

    public function getShowCatName(): ?bool
    {
        return $this->showCatName;
    }

    /**
     * Set show cat name
     * Specifies that the category name should be shown in data labels.
     */
    public function setShowCatName(?bool $showCategoryName): self
    {
        $this->showCatName = $showCategoryName;

        return $this;
    }

    public function getShowSerName(): ?bool
    {
        return $this->showSerName;
    }

    /**
     * Set show data series name.
     * Specifies that the series name should be shown in data labels.
     */
    public function setShowSerName(?bool $showSeriesName): self
    {
        $this->showSerName = $showSeriesName;

        return $this;
    }

    public function getShowPercent(): ?bool
    {
        return $this->showPercent;
    }

    /**
     * Set show percentage.
     * Specifies that the percentage should be shown in data labels.
     */
    public function setShowPercent(?bool $showPercentage): self
    {
        $this->showPercent = $showPercentage;

        return $this;
    }

    public function getShowBubbleSize(): ?bool
    {
        return $this->showBubbleSize;
    }

    /**
     * Set show bubble size.
     * Specifies that the bubble size should be shown in data labels.
     */
    public function setShowBubbleSize(?bool $showBubbleSize): self
    {
        $this->showBubbleSize = $showBubbleSize;

        return $this;
    }

    public function getShowLeaderLines(): ?bool
    {
        return $this->showLeaderLines;
    }

    /**
     * Set show leader lines.
     * Specifies that leader lines should be shown in data labels.
     */
    public function setShowLeaderLines(?bool $showLeaderLines): self
    {
        $this->showLeaderLines = $showLeaderLines;

        return $this;
    }

    public function getLabelFillColor(): ?ChartColor
    {
        return $this->labelFillColor;
    }

    public function setLabelFillColor(?ChartColor $chartColor): self
    {
        $this->labelFillColor = $chartColor;

        return $this;
    }

    public function getLabelBorderColor(): ?ChartColor
    {
        return $this->labelBorderColor;
    }

    public function setLabelBorderColor(?ChartColor $chartColor): self
    {
        $this->labelBorderColor = $chartColor;

        return $this;
    }

    public function getLabelFontColor(): ?ChartColor
    {
        return $this->labelFontColor;
    }

    public function setLabelFontColor(?ChartColor $chartColor): self
    {
        $this->labelFontColor = $chartColor;

        return $this;
    }

    public function getDLblPos(): string
    {
        return $this->dLblPos;
    }

    public function setDLblPos(string $dLblPos): self
    {
        $this->dLblPos = $dLblPos;

        return $this;
    }

    public function getNumFmtCode(): string
    {
        return $this->numFmtCode;
    }

    public function setNumFmtCode(string $numFmtCode): self
    {
        $this->numFmtCode = $numFmtCode;

        return $this;
    }

    public function getNumFmtLinked(): bool
    {
        return $this->numFmtLinked;
    }

    public function setNumFmtLinked(bool $numFmtLinked): self
    {
        $this->numFmtLinked = $numFmtLinked;

        return $this;
    }
}
