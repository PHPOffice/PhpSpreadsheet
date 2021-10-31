<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class Layout
{
    /**
     * layoutTarget.
     *
     * @var string
     */
    private $layoutTarget;

    /**
     * X Mode.
     *
     * @var string
     */
    private $xMode;

    /**
     * Y Mode.
     *
     * @var string
     */
    private $yMode;

    /**
     * X-Position.
     *
     * @var float
     */
    private $xPos;

    /**
     * Y-Position.
     *
     * @var float
     */
    private $yPos;

    /**
     * width.
     *
     * @var float
     */
    private $width;

    /**
     * height.
     *
     * @var float
     */
    private $height;

    /**
     * show legend key
     * Specifies that legend keys should be shown in data labels.
     *
     * @var bool
     */
    private $showLegendKey;

    /**
     * show value
     * Specifies that the value should be shown in a data label.
     *
     * @var bool
     */
    private $showVal;

    /**
     * show category name
     * Specifies that the category name should be shown in the data label.
     *
     * @var bool
     */
    private $showCatName;

    /**
     * show data series name
     * Specifies that the series name should be shown in the data label.
     *
     * @var bool
     */
    private $showSerName;

    /**
     * show percentage
     * Specifies that the percentage should be shown in the data label.
     *
     * @var bool
     */
    private $showPercent;

    /**
     * show bubble size.
     *
     * @var bool
     */
    private $showBubbleSize;

    /**
     * show leader lines
     * Specifies that leader lines should be shown for the data label.
     *
     * @var bool
     */
    private $showLeaderLines;

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
    }

    /**
     * Get Layout Target.
     *
     * @return string
     */
    public function getLayoutTarget()
    {
        return $this->layoutTarget;
    }

    /**
     * Set Layout Target.
     *
     * @param string $target
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
     * @return string
     */
    public function getXMode()
    {
        return $this->xMode;
    }

    /**
     * Set X-Mode.
     *
     * @param string $mode
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
     * @return string
     */
    public function getYMode()
    {
        return $this->yMode;
    }

    /**
     * Set Y-Mode.
     *
     * @param string $mode
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
     * @return number
     */
    public function getXPosition()
    {
        return $this->xPos;
    }

    /**
     * Set X-Position.
     *
     * @param float $position
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
     * @return number
     */
    public function getYPosition()
    {
        return $this->yPos;
    }

    /**
     * Set Y-Position.
     *
     * @param float $position
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
     * @return number
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width.
     *
     * @param float $width
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
     * @return number
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height.
     *
     * @param float $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get show legend key.
     *
     * @return bool
     */
    public function getShowLegendKey()
    {
        return $this->showLegendKey;
    }

    /**
     * Set show legend key
     * Specifies that legend keys should be shown in data labels.
     *
     * @param bool $showLegendKey Show legend key
     *
     * @return $this
     */
    public function setShowLegendKey($showLegendKey)
    {
        $this->showLegendKey = $showLegendKey;

        return $this;
    }

    /**
     * Get show value.
     *
     * @return bool
     */
    public function getShowVal()
    {
        return $this->showVal;
    }

    /**
     * Set show val
     * Specifies that the value should be shown in data labels.
     *
     * @param bool $showDataLabelValues Show val
     *
     * @return $this
     */
    public function setShowVal($showDataLabelValues)
    {
        $this->showVal = $showDataLabelValues;

        return $this;
    }

    /**
     * Get show category name.
     *
     * @return bool
     */
    public function getShowCatName()
    {
        return $this->showCatName;
    }

    /**
     * Set show cat name
     * Specifies that the category name should be shown in data labels.
     *
     * @param bool $showCategoryName Show cat name
     *
     * @return $this
     */
    public function setShowCatName($showCategoryName)
    {
        $this->showCatName = $showCategoryName;

        return $this;
    }

    /**
     * Get show data series name.
     *
     * @return bool
     */
    public function getShowSerName()
    {
        return $this->showSerName;
    }

    /**
     * Set show ser name
     * Specifies that the series name should be shown in data labels.
     *
     * @param bool $showSeriesName Show series name
     *
     * @return $this
     */
    public function setShowSerName($showSeriesName)
    {
        $this->showSerName = $showSeriesName;

        return $this;
    }

    /**
     * Get show percentage.
     *
     * @return bool
     */
    public function getShowPercent()
    {
        return $this->showPercent;
    }

    /**
     * Set show percentage
     * Specifies that the percentage should be shown in data labels.
     *
     * @param bool $showPercentage Show percentage
     *
     * @return $this
     */
    public function setShowPercent($showPercentage)
    {
        $this->showPercent = $showPercentage;

        return $this;
    }

    /**
     * Get show bubble size.
     *
     * @return bool
     */
    public function getShowBubbleSize()
    {
        return $this->showBubbleSize;
    }

    /**
     * Set show bubble size
     * Specifies that the bubble size should be shown in data labels.
     *
     * @param bool $showBubbleSize Show bubble size
     *
     * @return $this
     */
    public function setShowBubbleSize($showBubbleSize)
    {
        $this->showBubbleSize = $showBubbleSize;

        return $this;
    }

    /**
     * Get show leader lines.
     *
     * @return bool
     */
    public function getShowLeaderLines()
    {
        return $this->showLeaderLines;
    }

    /**
     * Set show leader lines
     * Specifies that leader lines should be shown in data labels.
     *
     * @param bool $showLeaderLines Show leader lines
     *
     * @return $this
     */
    public function setShowLeaderLines($showLeaderLines)
    {
        $this->showLeaderLines = $showLeaderLines;

        return $this;
    }
}
