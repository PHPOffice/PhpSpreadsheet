<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category    PhpSpreadsheet
 * @copyright   Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version     ##VERSION##, ##DATE##
 */
class PlotArea
{
    /**
     * PlotArea Layout
     *
     * @var Layout
     */
    private $layout = null;

    /**
     * Plot Series
     *
     * @var array of DataSeries
     */
    private $plotSeries = [];

    /**
     * Create a new PlotArea
     */
    public function __construct(Layout $layout = null, $plotSeries = [])
    {
        $this->layout = $layout;
        $this->plotSeries = $plotSeries;
    }

    /**
     * Get Layout
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Get Number of Plot Groups
     *
     * @return array of DataSeries
     */
    public function getPlotGroupCount()
    {
        return count($this->plotSeries);
    }

    /**
     * Get Number of Plot Series
     *
     * @return int
     */
    public function getPlotSeriesCount()
    {
        $seriesCount = 0;
        foreach ($this->plotSeries as $plot) {
            $seriesCount += $plot->getPlotSeriesCount();
        }

        return $seriesCount;
    }

    /**
     * Get Plot Series
     *
     * @return array of DataSeries
     */
    public function getPlotGroup()
    {
        return $this->plotSeries;
    }

    /**
     * Get Plot Series by Index
     *
     * @return DataSeries
     */
    public function getPlotGroupByIndex($index)
    {
        return $this->plotSeries[$index];
    }

    /**
     * Set Plot Series
     *
     * @param  DataSeries[]
     * @return PlotArea
     */
    public function setPlotSeries($plotSeries = [])
    {
        $this->plotSeries = $plotSeries;

        return $this;
    }

    public function refresh(\PhpOffice\PhpSpreadsheet\Worksheet $worksheet)
    {
        foreach ($this->plotSeries as $plotSeries) {
            $plotSeries->refresh($worksheet);
        }
    }
}
