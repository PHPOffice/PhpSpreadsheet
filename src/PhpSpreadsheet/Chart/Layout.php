<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Style\Font;

class Layout
{
    /**
     * layoutTarget.
     */
    private ?string $layoutTarget = null;

    /**
     * X Mode.
     */
    private ?string $xMode = null;

    /**
     * Y Mode.
     */
    private ?string $yMode = null;

    /**
     * X-Position.
     */
    private ?float $xPos = null;

    /**
     * Y-Position.
     */
    private ?float $yPos = null;

    /**
     * width.
     */
    private ?float $width = null;

    /**
     * height.
     */
    private ?float $height = null;

    /**
     * Position - t=top.
     */
    private string $dLblPos = '';

    private string $numFmtCode = '';

    private bool $numFmtLinked = false;

    /**
     * show legend key
     * Specifies that legend keys should be shown in data labels.
     */
    private ?bool $showLegendKey = null;

    /**
     * show value
     * Specifies that the value should be shown in a data label.
     */
    private ?bool $showVal = null;

    /**
     * show category name
     * Specifies that the category name should be shown in the data label.
     */
    private ?bool $showCatName = null;

    /**
     * show data series name
     * Specifies that the series name should be shown in the data label.
     */
    private ?bool $showSerName = null;

    /**
     * show percentage
     * Specifies that the percentage should be shown in the data label.
     */
    private ?bool $showPercent = null;

    /**
     * show bubble size.
     */
    private ?bool $showBubbleSize = null;

    /**
     * show leader lines
     * Specifies that leader lines should be shown for the data label.
     */
    private ?bool $showLeaderLines = null;

    private ?ChartColor $labelFillColor = null;

    private ?ChartColor $labelBorderColor = null;

    private ?Font $labelFont = null;

    private ?Properties $labelEffects = null;

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
        $labelFont = $layout['labelFont'] ?? null;
        if ($labelFont instanceof Font) {
            $this->labelFont = $labelFont;
        }
        $labelFontColor = $layout['labelFontColor'] ?? null;
        if ($labelFontColor instanceof ChartColor) {
            $this->setLabelFontColor($labelFontColor);
        }
        $labelEffects = $layout['labelEffects'] ?? null;
        if ($labelEffects instanceof Properties) {
            $this->labelEffects = $labelEffects;
        }
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
     * @return $this
     */
    public function setLayoutTarget(?string $target): static
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
    public function setXMode($mode): static
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
    public function setYMode($mode): static
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
     * @return $this
     */
    public function setXPosition(float $position): static
    {
        $this->xPos = $position;

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
     * @return $this
     */
    public function setYPosition(float $position): static
    {
        $this->yPos = $position;

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
     * @return $this
     */
    public function setWidth(?float $width): static
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
     * @return $this
     */
    public function setHeight(?float $height): static
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

    public function getLabelFont(): ?Font
    {
        return $this->labelFont;
    }

    public function getLabelEffects(): ?Properties
    {
        return $this->labelEffects;
    }

    public function getLabelFontColor(): ?ChartColor
    {
        if ($this->labelFont === null) {
            return null;
        }

        return $this->labelFont->getChartColor();
    }

    public function setLabelFontColor(?ChartColor $chartColor): self
    {
        if ($this->labelFont === null) {
            $this->labelFont = new Font();
            $this->labelFont->setSize(null, true);
        }
        $this->labelFont->setChartColorFromObject($chartColor);

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
