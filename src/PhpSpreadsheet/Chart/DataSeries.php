<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSeries
{
    const TYPE_BARCHART = 'barChart';
    const TYPE_BARCHART_3D = 'bar3DChart';
    const TYPE_LINECHART = 'lineChart';
    const TYPE_LINECHART_3D = 'line3DChart';
    const TYPE_AREACHART = 'areaChart';
    const TYPE_AREACHART_3D = 'area3DChart';
    const TYPE_PIECHART = 'pieChart';
    const TYPE_PIECHART_3D = 'pie3DChart';
    const TYPE_DOUGHNUTCHART = 'doughnutChart';
    const TYPE_DONUTCHART = self::TYPE_DOUGHNUTCHART; // Synonym
    const TYPE_SCATTERCHART = 'scatterChart';
    const TYPE_SURFACECHART = 'surfaceChart';
    const TYPE_SURFACECHART_3D = 'surface3DChart';
    const TYPE_RADARCHART = 'radarChart';
    const TYPE_BUBBLECHART = 'bubbleChart';
    const TYPE_STOCKCHART = 'stockChart';
    const TYPE_CANDLECHART = self::TYPE_STOCKCHART; // Synonym

    const GROUPING_CLUSTERED = 'clustered';
    const GROUPING_STACKED = 'stacked';
    const GROUPING_PERCENT_STACKED = 'percentStacked';
    const GROUPING_STANDARD = 'standard';

    const DIRECTION_BAR = 'bar';
    const DIRECTION_HORIZONTAL = self::DIRECTION_BAR;
    const DIRECTION_COL = 'col';
    const DIRECTION_COLUMN = self::DIRECTION_COL;
    const DIRECTION_VERTICAL = self::DIRECTION_COL;

    const STYLE_LINEMARKER = 'lineMarker';
    const STYLE_SMOOTHMARKER = 'smoothMarker';
    const STYLE_MARKER = 'marker';
    const STYLE_FILLED = 'filled';

    const EMPTY_AS_GAP = 'gap';
    const EMPTY_AS_ZERO = 'zero';
    const EMPTY_AS_SPAN = 'span';

    /**
     * Series Plot Type.
     *
     * @var string
     */
    private $plotType;

    /**
     * Plot Grouping Type.
     *
     * @var string
     */
    private $plotGrouping;

    /**
     * Plot Direction.
     *
     * @var string
     */
    private $plotDirection;

    /**
     * Plot Style.
     *
     * @var null|string
     */
    private $plotStyle;

    /**
     * Order of plots in Series.
     *
     * @var int[]
     */
    private array $plotOrder;

    /**
     * Plot Label.
     *
     * @var DataSeriesValues[]
     */
    private array $plotLabel;

    /**
     * Plot Category.
     *
     * @var DataSeriesValues[]
     */
    private array $plotCategory;

    /**
     * Smooth Line. Must be specified for both DataSeries and DataSeriesValues.
     *
     * @var bool
     */
    private $smoothLine;

    /**
     * Plot Values.
     *
     * @var DataSeriesValues[]
     */
    private array $plotValues;

    /**
     * Plot Bubble Sizes.
     *
     * @var DataSeriesValues[]
     */
    private $plotBubbleSizes = [];

    /**
     * Create a new DataSeries.
     *
     * @param null|mixed $plotType
     * @param null|mixed $plotGrouping
     * @param int[] $plotOrder
     * @param DataSeriesValues[] $plotLabel
     * @param DataSeriesValues[] $plotCategory
     * @param DataSeriesValues[] $plotValues
     * @param null|string $plotDirection
     * @param null|bool $smoothLine null treated as false
     * @param null|string $plotStyle
     */
    public function __construct($plotType = null, $plotGrouping = null, array $plotOrder = [], array $plotLabel = [], array $plotCategory = [], array $plotValues = [], $plotDirection = null, $smoothLine = false, $plotStyle = null)
    {
        $this->plotType = $plotType;
        $this->plotGrouping = $plotGrouping;
        $this->plotOrder = $plotOrder;
        $keys = array_keys($plotValues);
        $this->plotValues = $plotValues;
        if (!isset($plotLabel[$keys[0]])) {
            $plotLabel[$keys[0]] = new DataSeriesValues();
        }
        $this->plotLabel = $plotLabel;

        if (!isset($plotCategory[$keys[0]])) {
            $plotCategory[$keys[0]] = new DataSeriesValues();
        }
        $this->plotCategory = $plotCategory;

        $this->smoothLine = (bool) $smoothLine;
        $this->plotStyle = $plotStyle;

        if ($plotDirection === null) {
            $plotDirection = self::DIRECTION_COL;
        }
        $this->plotDirection = $plotDirection;
    }

    /**
     * Get Plot Type.
     *
     * @return string
     */
    public function getPlotType()
    {
        return $this->plotType;
    }

    /**
     * Set Plot Type.
     *
     * @param string $plotType
     *
     * @return $this
     */
    public function setPlotType($plotType): static
    {
        $this->plotType = $plotType;

        return $this;
    }

    /**
     * Get Plot Grouping Type.
     *
     * @return string
     */
    public function getPlotGrouping()
    {
        return $this->plotGrouping;
    }

    /**
     * Set Plot Grouping Type.
     *
     * @param string $groupingType
     *
     * @return $this
     */
    public function setPlotGrouping($groupingType): static
    {
        $this->plotGrouping = $groupingType;

        return $this;
    }

    /**
     * Get Plot Direction.
     *
     * @return string
     */
    public function getPlotDirection()
    {
        return $this->plotDirection;
    }

    /**
     * Set Plot Direction.
     *
     * @param string $plotDirection
     *
     * @return $this
     */
    public function setPlotDirection($plotDirection): static
    {
        $this->plotDirection = $plotDirection;

        return $this;
    }

    /**
     * Get Plot Order.
     *
     * @return int[]
     */
    public function getPlotOrder()
    {
        return $this->plotOrder;
    }

    /**
     * Get Plot Labels.
     *
     * @return DataSeriesValues[]
     */
    public function getPlotLabels()
    {
        return $this->plotLabel;
    }

    /**
     * Get Plot Label by Index.
     *
     * @return DataSeriesValues|false
     */
    public function getPlotLabelByIndex(mixed $index)
    {
        $keys = array_keys($this->plotLabel);
        if (in_array($index, $keys)) {
            return $this->plotLabel[$index];
        }

        return false;
    }

    /**
     * Get Plot Categories.
     *
     * @return DataSeriesValues[]
     */
    public function getPlotCategories()
    {
        return $this->plotCategory;
    }

    /**
     * Get Plot Category by Index.
     *
     * @return DataSeriesValues|false
     */
    public function getPlotCategoryByIndex(mixed $index)
    {
        $keys = array_keys($this->plotCategory);
        if (in_array($index, $keys)) {
            return $this->plotCategory[$index];
        } elseif (isset($keys[$index])) {
            return $this->plotCategory[$keys[$index]];
        }

        return false;
    }

    /**
     * Get Plot Style.
     *
     * @return null|string
     */
    public function getPlotStyle()
    {
        return $this->plotStyle;
    }

    /**
     * Set Plot Style.
     *
     * @param null|string $plotStyle
     *
     * @return $this
     */
    public function setPlotStyle($plotStyle): static
    {
        $this->plotStyle = $plotStyle;

        return $this;
    }

    /**
     * Get Plot Values.
     *
     * @return DataSeriesValues[]
     */
    public function getPlotValues()
    {
        return $this->plotValues;
    }

    /**
     * Get Plot Values by Index.
     *
     * @return DataSeriesValues|false
     */
    public function getPlotValuesByIndex(mixed $index)
    {
        $keys = array_keys($this->plotValues);
        if (in_array($index, $keys)) {
            return $this->plotValues[$index];
        }

        return false;
    }

    /**
     * Get Plot Bubble Sizes.
     *
     * @return DataSeriesValues[]
     */
    public function getPlotBubbleSizes(): array
    {
        return $this->plotBubbleSizes;
    }

    /**
     * Set Plot Bubble Sizes.
     *
     * @param DataSeriesValues[] $plotBubbleSizes
     */
    public function setPlotBubbleSizes(array $plotBubbleSizes): self
    {
        $this->plotBubbleSizes = $plotBubbleSizes;

        return $this;
    }

    /**
     * Get Number of Plot Series.
     */
    public function getPlotSeriesCount(): int
    {
        return count($this->plotValues);
    }

    /**
     * Get Smooth Line.
     *
     * @return bool
     */
    public function getSmoothLine()
    {
        return $this->smoothLine;
    }

    /**
     * Set Smooth Line.
     *
     * @param bool $smoothLine
     *
     * @return $this
     */
    public function setSmoothLine($smoothLine): static
    {
        $this->smoothLine = $smoothLine;

        return $this;
    }

    public function refresh(Worksheet $worksheet): void
    {
        foreach ($this->plotValues as $plotValues) {
            if ($plotValues !== null) {
                $plotValues->refresh($worksheet, true);
            }
        }
        foreach ($this->plotLabel as $plotValues) {
            if ($plotValues !== null) {
                $plotValues->refresh($worksheet, true);
            }
        }
        foreach ($this->plotCategory as $plotValues) {
            if ($plotValues !== null) {
                $plotValues->refresh($worksheet, false);
            }
        }
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $plotLabels = $this->plotLabel;
        $this->plotLabel = [];
        foreach ($plotLabels as $plotLabel) {
            $this->plotLabel[] = $plotLabel;
        }
        $plotCategories = $this->plotCategory;
        $this->plotCategory = [];
        foreach ($plotCategories as $plotCategory) {
            $this->plotCategory[] = clone $plotCategory;
        }
        $plotValues = $this->plotValues;
        $this->plotValues = [];
        foreach ($plotValues as $plotValue) {
            $this->plotValues[] = clone $plotValue;
        }
        $plotBubbleSizes = $this->plotBubbleSizes;
        $this->plotBubbleSizes = [];
        foreach ($plotBubbleSizes as $plotBubbleSize) {
            $this->plotBubbleSizes[] = clone $plotBubbleSize;
        }
    }
}
