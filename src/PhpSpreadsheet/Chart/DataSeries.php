<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category    PhpSpreadsheet
 *
 * @copyright    Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
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
    const TYPE_DOUGHTNUTCHART = 'doughnutChart';
    const TYPE_DONUTCHART = self::TYPE_DOUGHTNUTCHART; //    Synonym
    const TYPE_SCATTERCHART = 'scatterChart';
    const TYPE_SURFACECHART = 'surfaceChart';
    const TYPE_SURFACECHART_3D = 'surface3DChart';
    const TYPE_RADARCHART = 'radarChart';
    const TYPE_BUBBLECHART = 'bubbleChart';
    const TYPE_STOCKCHART = 'stockChart';
    const TYPE_CANDLECHART = self::TYPE_STOCKCHART; //    Synonym

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
     * @var string
     */
    private $plotStyle;

    /**
     * Order of plots in Series.
     *
     * @var array of integer
     */
    private $plotOrder = [];

    /**
     * Plot Label.
     *
     * @var array of DataSeriesValues
     */
    private $plotLabel = [];

    /**
     * Plot Category.
     *
     * @var array of DataSeriesValues
     */
    private $plotCategory = [];

    /**
     * Smooth Line.
     *
     * @var bool
     */
    private $smoothLine;

    /**
     * Plot Values.
     *
     * @var array of DataSeriesValues
     */
    private $plotValues = [];

    /**
     * Create a new DataSeries.
     *
     * @param null|mixed $plotType
     * @param null|mixed $plotGrouping
     * @param mixed $plotOrder
     * @param mixed $plotLabel
     * @param mixed $plotCategory
     * @param mixed $plotValues
     * @param null|mixed $plotDirection
     * @param null|mixed $smoothLine
     * @param null|mixed $plotStyle
     */
    public function __construct($plotType = null, $plotGrouping = null, $plotOrder = [], $plotLabel = [], $plotCategory = [], $plotValues = [], $plotDirection = null, $smoothLine = null, $plotStyle = null)
    {
        $this->plotType = $plotType;
        $this->plotGrouping = $plotGrouping;
        $this->plotOrder = $plotOrder;
        $keys = array_keys($plotValues);
        $this->plotValues = $plotValues;
        if ((count($plotLabel) == 0) || (is_null($plotLabel[$keys[0]]))) {
            $plotLabel[$keys[0]] = new DataSeriesValues();
        }

        $this->plotLabel = $plotLabel;
        if ((count($plotCategory) == 0) || (is_null($plotCategory[$keys[0]]))) {
            $plotCategory[$keys[0]] = new DataSeriesValues();
        }
        $this->plotCategory = $plotCategory;
        $this->smoothLine = $smoothLine;
        $this->plotStyle = $plotStyle;

        if (is_null($plotDirection)) {
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
     * @return DataSeries
     */
    public function setPlotType($plotType)
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
     * @return DataSeries
     */
    public function setPlotGrouping($groupingType)
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
     * @return DataSeries
     */
    public function setPlotDirection($plotDirection)
    {
        $this->plotDirection = $plotDirection;

        return $this;
    }

    /**
     * Get Plot Order.
     *
     * @return string
     */
    public function getPlotOrder()
    {
        return $this->plotOrder;
    }

    /**
     * Get Plot Labels.
     *
     * @return array of DataSeriesValues
     */
    public function getPlotLabels()
    {
        return $this->plotLabel;
    }

    /**
     * Get Plot Label by Index.
     *
     * @param mixed $index
     *
     * @return DataSeriesValues
     */
    public function getPlotLabelByIndex($index)
    {
        $keys = array_keys($this->plotLabel);
        if (in_array($index, $keys)) {
            return $this->plotLabel[$index];
        } elseif (isset($keys[$index])) {
            return $this->plotLabel[$keys[$index]];
        }

        return false;
    }

    /**
     * Get Plot Categories.
     *
     * @return array of DataSeriesValues
     */
    public function getPlotCategories()
    {
        return $this->plotCategory;
    }

    /**
     * Get Plot Category by Index.
     *
     * @param mixed $index
     *
     * @return DataSeriesValues
     */
    public function getPlotCategoryByIndex($index)
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
     * @return string
     */
    public function getPlotStyle()
    {
        return $this->plotStyle;
    }

    /**
     * Set Plot Style.
     *
     * @param string $plotStyle
     *
     * @return DataSeries
     */
    public function setPlotStyle($plotStyle)
    {
        $this->plotStyle = $plotStyle;

        return $this;
    }

    /**
     * Get Plot Values.
     *
     * @return array of DataSeriesValues
     */
    public function getPlotValues()
    {
        return $this->plotValues;
    }

    /**
     * Get Plot Values by Index.
     *
     * @param mixed $index
     *
     * @return DataSeriesValues
     */
    public function getPlotValuesByIndex($index)
    {
        $keys = array_keys($this->plotValues);
        if (in_array($index, $keys)) {
            return $this->plotValues[$index];
        } elseif (isset($keys[$index])) {
            return $this->plotValues[$keys[$index]];
        }

        return false;
    }

    /**
     * Get Number of Plot Series.
     *
     * @return int
     */
    public function getPlotSeriesCount()
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
     * @return DataSeries
     */
    public function setSmoothLine($smoothLine)
    {
        $this->smoothLine = $smoothLine;

        return $this;
    }

    public function refresh(Worksheet $worksheet)
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
}
