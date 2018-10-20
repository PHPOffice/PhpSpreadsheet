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
     *
     * @param array $layout
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
     * @param string $value
     *
     * @return Layout
     */
    public function setLayoutTarget($value)
    {
        $this->layoutTarget = $value;

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
     * @param X-Mode $value
     *
     * @return Layout
     */
    public function setXMode($value)
    {
        $this->xMode = $value;

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
     * @param Y-Mode $value
     *
     * @return Layout
     */
    public function setYMode($value)
    {
        $this->yMode = $value;

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
     * @param X-Position $value
     *
     * @return Layout
     */
    public function setXPosition($value)
    {
        $this->xPos = $value;

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
     * @param Y-Position $value
     *
     * @return Layout
     */
    public function setYPosition($value)
    {
        $this->yPos = $value;

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
     * @param float $value
     *
     * @return Layout
     */
    public function setWidth($value)
    {
        $this->width = $value;

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
     * @param float $value
     *
     * @return Layout
     */
    public function setHeight($value)
    {
        $this->height = $value;

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
     * @param bool $value Show legend key
     *
     * @return Layout
     */
    public function setShowLegendKey($value)
    {
        $this->showLegendKey = $value;

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
     * @param bool $value Show val
     *
     * @return Layout
     */
    public function setShowVal($value)
    {
        $this->showVal = $value;

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
     * @param bool $value Show cat name
     *
     * @return Layout
     */
    public function setShowCatName($value)
    {
        $this->showCatName = $value;

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
     * @param bool $value Show series name
     *
     * @return Layout
     */
    public function setShowSerName($value)
    {
        $this->showSerName = $value;

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
     * @param bool $value Show percentage
     *
     * @return Layout
     */
    public function setShowPercent($value)
    {
        $this->showPercent = $value;

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
     * @param bool $value Show bubble size
     *
     * @return Layout
     */
    public function setShowBubbleSize($value)
    {
        $this->showBubbleSize = $value;

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
     * @param bool $value Show leader lines
     *
     * @return Layout
     */
    public function setShowLeaderLines($value)
    {
        $this->showLeaderLines = $value;

        return $this;
    }
}
