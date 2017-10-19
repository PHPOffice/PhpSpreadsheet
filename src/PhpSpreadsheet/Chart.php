<?php

namespace PhpOffice\PhpSpreadsheet;

class Chart
{
    /**
     * Chart Name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Worksheet.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Chart Title.
     *
     * @var Chart\Title
     */
    private $title;

    /**
     * Chart Legend.
     *
     * @var Chart\Legend
     */
    private $legend;

    /**
     * X-Axis Label.
     *
     * @var Chart\Title
     */
    private $xAxisLabel;

    /**
     * Y-Axis Label.
     *
     * @var Chart\Title
     */
    private $yAxisLabel;

    /**
     * Chart Plot Area.
     *
     * @var Chart\PlotArea
     */
    private $plotArea;

    /**
     * Plot Visible Only.
     *
     * @var bool
     */
    private $plotVisibleOnly = true;

    /**
     * Display Blanks as.
     *
     * @var string
     */
    private $displayBlanksAs = '0';

    /**
     * Chart Asix Y as.
     *
     * @var Chart\Axis
     */
    private $yAxis;

    /**
     * Chart Asix X as.
     *
     * @var Chart\Axis
     */
    private $xAxis;

    /**
     * Chart Major Gridlines as.
     *
     * @var Chart\GridLines
     */
    private $majorGridlines;

    /**
     * Chart Minor Gridlines as.
     *
     * @var Chart\GridLines
     */
    private $minorGridlines;

    /**
     * Top-Left Cell Position.
     *
     * @var string
     */
    private $topLeftCellRef = 'A1';

    /**
     * Top-Left X-Offset.
     *
     * @var int
     */
    private $topLeftXOffset = 0;

    /**
     * Top-Left Y-Offset.
     *
     * @var int
     */
    private $topLeftYOffset = 0;

    /**
     * Bottom-Right Cell Position.
     *
     * @var string
     */
    private $bottomRightCellRef = 'A1';

    /**
     * Bottom-Right X-Offset.
     *
     * @var int
     */
    private $bottomRightXOffset = 10;

    /**
     * Bottom-Right Y-Offset.
     *
     * @var int
     */
    private $bottomRightYOffset = 10;

    /**
     * Create a new Chart.
     *
     * @param mixed $name
     * @param mixed $plotVisibleOnly
     * @param mixed $displayBlanksAs
     */
    public function __construct($name, Chart\Title $title = null, Chart\Legend $legend = null, Chart\PlotArea $plotArea = null, $plotVisibleOnly = true, $displayBlanksAs = '0', Chart\Title $xAxisLabel = null, Chart\Title $yAxisLabel = null, Chart\Axis $xAxis = null, Chart\Axis $yAxis = null, Chart\GridLines $majorGridlines = null, Chart\GridLines $minorGridlines = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->legend = $legend;
        $this->xAxisLabel = $xAxisLabel;
        $this->yAxisLabel = $yAxisLabel;
        $this->plotArea = $plotArea;
        $this->plotVisibleOnly = $plotVisibleOnly;
        $this->displayBlanksAs = $displayBlanksAs;
        $this->xAxis = $xAxis;
        $this->yAxis = $yAxis;
        $this->majorGridlines = $majorGridlines;
        $this->minorGridlines = $minorGridlines;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Worksheet.
     *
     * @return Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set Worksheet.
     *
     * @param Worksheet $pValue
     *
     * @throws Chart\Exception
     *
     * @return Chart
     */
    public function setWorksheet(Worksheet $pValue = null)
    {
        $this->worksheet = $pValue;

        return $this;
    }

    /**
     * Get Title.
     *
     * @return Chart\Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param Chart\Title $title
     *
     * @return Chart
     */
    public function setTitle(Chart\Title $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Legend.
     *
     * @return Chart\Legend
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set Legend.
     *
     * @param Chart\Legend $legend
     *
     * @return Chart
     */
    public function setLegend(Chart\Legend $legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get X-Axis Label.
     *
     * @return Chart\Title
     */
    public function getXAxisLabel()
    {
        return $this->xAxisLabel;
    }

    /**
     * Set X-Axis Label.
     *
     * @param Chart\Title $label
     *
     * @return Chart
     */
    public function setXAxisLabel(Chart\Title $label)
    {
        $this->xAxisLabel = $label;

        return $this;
    }

    /**
     * Get Y-Axis Label.
     *
     * @return Chart\Title
     */
    public function getYAxisLabel()
    {
        return $this->yAxisLabel;
    }

    /**
     * Set Y-Axis Label.
     *
     * @param Chart\Title $label
     *
     * @return Chart
     */
    public function setYAxisLabel(Chart\Title $label)
    {
        $this->yAxisLabel = $label;

        return $this;
    }

    /**
     * Get Plot Area.
     *
     * @return Chart\PlotArea
     */
    public function getPlotArea()
    {
        return $this->plotArea;
    }

    /**
     * Get Plot Visible Only.
     *
     * @return bool
     */
    public function getPlotVisibleOnly()
    {
        return $this->plotVisibleOnly;
    }

    /**
     * Set Plot Visible Only.
     *
     * @param bool $plotVisibleOnly
     *
     * @return Chart
     */
    public function setPlotVisibleOnly($plotVisibleOnly)
    {
        $this->plotVisibleOnly = $plotVisibleOnly;

        return $this;
    }

    /**
     * Get Display Blanks as.
     *
     * @return string
     */
    public function getDisplayBlanksAs()
    {
        return $this->displayBlanksAs;
    }

    /**
     * Set Display Blanks as.
     *
     * @param string $displayBlanksAs
     *
     * @return Chart
     */
    public function setDisplayBlanksAs($displayBlanksAs)
    {
        $this->displayBlanksAs = $displayBlanksAs;
    }

    /**
     * Get yAxis.
     *
     * @return Chart\Axis
     */
    public function getChartAxisY()
    {
        if ($this->yAxis !== null) {
            return $this->yAxis;
        }

        return new Chart\Axis();
    }

    /**
     * Get xAxis.
     *
     * @return Chart\Axis
     */
    public function getChartAxisX()
    {
        if ($this->xAxis !== null) {
            return $this->xAxis;
        }

        return new Chart\Axis();
    }

    /**
     * Get Major Gridlines.
     *
     * @return Chart\GridLines
     */
    public function getMajorGridlines()
    {
        if ($this->majorGridlines !== null) {
            return $this->majorGridlines;
        }

        return new Chart\GridLines();
    }

    /**
     * Get Minor Gridlines.
     *
     * @return Chart\GridLines
     */
    public function getMinorGridlines()
    {
        if ($this->minorGridlines !== null) {
            return $this->minorGridlines;
        }

        return new Chart\GridLines();
    }

    /**
     * Set the Top Left position for the chart.
     *
     * @param string $cell
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return Chart
     */
    public function setTopLeftPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->topLeftCellRef = $cell;
        if ($xOffset !== null) {
            $this->setTopLeftXOffset($xOffset);
        }
        if ($yOffset !== null) {
            $this->setTopLeftYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the top left position of the chart.
     *
     * @return array an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
     */
    public function getTopLeftPosition()
    {
        return [
            'cell' => $this->topLeftCellRef,
            'xOffset' => $this->topLeftXOffset,
            'yOffset' => $this->topLeftYOffset,
        ];
    }

    /**
     * Get the cell address where the top left of the chart is fixed.
     *
     * @return string
     */
    public function getTopLeftCell()
    {
        return $this->topLeftCellRef;
    }

    /**
     * Set the Top Left cell position for the chart.
     *
     * @param string $cell
     *
     * @return Chart
     */
    public function setTopLeftCell($cell)
    {
        $this->topLeftCellRef = $cell;

        return $this;
    }

    /**
     * Set the offset position within the Top Left cell for the chart.
     *
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return Chart
     */
    public function setTopLeftOffset($xOffset, $yOffset)
    {
        if ($xOffset !== null) {
            $this->setTopLeftXOffset($xOffset);
        }

        if ($yOffset !== null) {
            $this->setTopLeftYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the offset position within the Top Left cell for the chart.
     *
     * @return int[]
     */
    public function getTopLeftOffset()
    {
        return [
            'X' => $this->topLeftXOffset,
            'Y' => $this->topLeftYOffset,
        ];
    }

    public function setTopLeftXOffset($xOffset)
    {
        $this->topLeftXOffset = $xOffset;

        return $this;
    }

    public function getTopLeftXOffset()
    {
        return $this->topLeftXOffset;
    }

    public function setTopLeftYOffset($yOffset)
    {
        $this->topLeftYOffset = $yOffset;

        return $this;
    }

    public function getTopLeftYOffset()
    {
        return $this->topLeftYOffset;
    }

    /**
     * Set the Bottom Right position of the chart.
     *
     * @param string $cell
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return Chart
     */
    public function setBottomRightPosition($cell, $xOffset = null, $yOffset = null)
    {
        $this->bottomRightCellRef = $cell;
        if ($xOffset !== null) {
            $this->setBottomRightXOffset($xOffset);
        }
        if ($yOffset !== null) {
            $this->setBottomRightYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the bottom right position of the chart.
     *
     * @return array an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
     */
    public function getBottomRightPosition()
    {
        return [
            'cell' => $this->bottomRightCellRef,
            'xOffset' => $this->bottomRightXOffset,
            'yOffset' => $this->bottomRightYOffset,
        ];
    }

    public function setBottomRightCell($cell)
    {
        $this->bottomRightCellRef = $cell;

        return $this;
    }

    /**
     * Get the cell address where the bottom right of the chart is fixed.
     *
     * @return string
     */
    public function getBottomRightCell()
    {
        return $this->bottomRightCellRef;
    }

    /**
     * Set the offset position within the Bottom Right cell for the chart.
     *
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return Chart
     */
    public function setBottomRightOffset($xOffset, $yOffset)
    {
        if ($xOffset !== null) {
            $this->setBottomRightXOffset($xOffset);
        }

        if ($yOffset !== null) {
            $this->setBottomRightYOffset($yOffset);
        }

        return $this;
    }

    /**
     * Get the offset position within the Bottom Right cell for the chart.
     *
     * @return int[]
     */
    public function getBottomRightOffset()
    {
        return [
            'X' => $this->bottomRightXOffset,
            'Y' => $this->bottomRightYOffset,
        ];
    }

    public function setBottomRightXOffset($xOffset)
    {
        $this->bottomRightXOffset = $xOffset;

        return $this;
    }

    public function getBottomRightXOffset()
    {
        return $this->bottomRightXOffset;
    }

    public function setBottomRightYOffset($yOffset)
    {
        $this->bottomRightYOffset = $yOffset;

        return $this;
    }

    public function getBottomRightYOffset()
    {
        return $this->bottomRightYOffset;
    }

    public function refresh()
    {
        if ($this->worksheet !== null) {
            $this->plotArea->refresh($this->worksheet);
        }
    }

    public function render($outputDestination = null)
    {
        $libraryName = Settings::getChartRendererName();
        if ($libraryName === null) {
            return false;
        }
        //    Ensure that data series values are up-to-date before we render
        $this->refresh();

        $libraryPath = Settings::getChartRendererPath();
        $includePath = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $libraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $libraryPath);
        }

        $rendererName = '\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\' . $libraryName;
        $renderer = new $rendererName($this);

        if ($outputDestination == 'php://output') {
            $outputDestination = null;
        }

        return $renderer->render($outputDestination);
    }
}
