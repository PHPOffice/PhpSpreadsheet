<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
     * @var Title
     */
    private $title;

    /**
     * Chart Legend.
     *
     * @var Legend
     */
    private $legend;

    /**
     * X-Axis Label.
     *
     * @var Title
     */
    private $xAxisLabel;

    /**
     * Secondary X-Axis Label.
     *
     * @var Title
     */
    private $secondaryXAxisLabel;

    /**
     * Y-Axis Label.
     *
     * @var Title
     */
    private $yAxisLabel;

    /**
     * Secondary Y-Axis Label.
     *
     * @var Title
     */
    private $secondaryYAxisLabel;

    /**
     * Chart Plot Area.
     *
     * @var PlotArea
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
     * @var Axis
     */
    private $yAxis;

    /**
     * Secondary Chart Asix Y as.
     *
     * @var Title
     */
    private $secondaryYAxis;

    /**
     * Chart Asix X as.
     *
     * @var Axis
     */
    private $xAxis;

    /**
     * Secondary Chart Asix X as.
     *
     * @var Title
     */
    private $secondaryXAxis;

    /**
     * Chart Major Gridlines as.
     *
     * @var GridLines
     */
    private $majorGridlines;

    /**
     * Chart Minor Gridlines as.
     *
     * @var GridLines
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
     * @param null|Title $title
     * @param null|Legend $legend
     * @param null|PlotArea $plotArea
     * @param mixed $plotVisibleOnly
     * @param mixed $displayBlanksAs
     * @param null|Title $xAxisLabel
     * @param null|Title $yAxisLabel
     * @param null|Axis $xAxis
     * @param null|Axis $yAxis
     * @param null|GridLines $majorGridlines
     * @param null|GridLines $minorGridlines
     * @param null|Title $secondaryXAxisLabel
     * @param null|Title $secondaryYAxisLabel
     * @param null|Axis $secondaryXAxis
     * @param null|Axis $secondaryYAxis
     */
    public function __construct($name, Title $title = null, Legend $legend = null, PlotArea $plotArea = null, $plotVisibleOnly = true, $displayBlanksAs = '0', Title $xAxisLabel = null, Title $yAxisLabel = null, Axis $xAxis = null, Axis $yAxis = null, GridLines $majorGridlines = null, GridLines $minorGridlines = null, Title $secondaryXAxisLabel = null, Title $secondaryYAxisLabel = null, Axis $secondaryXAxis = null, Axis $secondaryYAxis = null)
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
        $this->secondaryXAxisLabel = $secondaryXAxisLabel;
        $this->secondaryYAxisLabel = $secondaryYAxisLabel;
        $this->secondaryXAxis = $secondaryXAxis;
        $this->secondaryYAxis = $secondaryYAxis;
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
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @param Title $title
     *
     * @return Chart
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Legend.
     *
     * @return Legend
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set Legend.
     *
     * @param Legend $legend
     *
     * @return Chart
     */
    public function setLegend(Legend $legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get X-Axis Label.
     *
     * @return Title
     */
    public function getXAxisLabel()
    {
        return $this->xAxisLabel;
    }

    /**
     * Set X-Axis Label.
     *
     * @param Title $label
     *
     * @return Chart
     */
    public function setXAxisLabel(Title $label)
    {
        $this->xAxisLabel = $label;

        return $this;
    }

    /**
     * Get Y-Axis Label.
     *
     * @return Title
     */
    public function getYAxisLabel()
    {
        return $this->yAxisLabel;
    }

    /**
     * Set Y-Axis Label.
     *
     * @param Title $label
     *
     * @return Chart
     */
    public function setYAxisLabel(Title $label)
    {
        $this->yAxisLabel = $label;

        return $this;
    }

    /**
     * Get Secondary X-Axis Label.
     *
     * @return Title
     */
    public function getSecondaryXAxisLabel()
    {
        return $this->secondaryXAxisLabel;
    }

    /**
     * Set Secondary X-Axis Label.
     *
     * @param Title $label
     *
     * @return Chart
     */
    public function setSecondaryXAxisLabel(Title $label)
    {
        $this->secondaryXAxisLabel = $label;

        return $this;
    }

    /**
     * Get Secondary Y-Axis Label.
     *
     * @return Title
     */
    public function getSecondaryYAxisLabel()
    {
        return $this->secondaryYAxisLabel;
    }

    /**
     * Set Secondary Y-Axis Label.
     *
     * @param Title $label
     *
     * @return Chart
     */
    public function setSecondaryYAxisLabel(Title $label)
    {
        $this->secondaryYAxisLabel = $label;

        return $this;
    }

    /**
     * Get Plot Area.
     *
     * @return PlotArea
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

        return $this;
    }

    /**
     * Get yAxis.
     *
     * @return Axis
     */
    public function getChartAxisY()
    {
        if ($this->yAxis === null) {
            $this->yAxis = new Axis();
        }

        return $this->yAxis;
    }

    /**
     * Get xAxis.
     *
     * @return Axis
     */
    public function getChartAxisX()
    {
        if ($this->xAxis === null) {
            $this->xAxis = new Axis();
        }

        return $this->xAxis;
    }

    /**
     * Get Secondary yAxis.
     *
     * @return Axis
     */
    public function getChartSecondaryAxisY()
    {
        return $this->secondaryYAxis;
    }

    /**
     * Set Secondary yAxis.
     *
     * @return Chart
     */
    public function setChartSecondaryAxisY(Axis $axis)
    {
        $this->secondaryYAxis = $axis;

        return $this;
    }

    /**
     * Get Secondary xAxis.
     *
     * @return Axis
     */
    public function getChartSecondaryAxisX()
    {
        return $this->secondaryXAxis;
    }

    /**
     * Set Secondary xAxis.
     *
     * @return Chart
     */
    public function setChartSecondaryAxisX(Axis $axis)
    {
        $this->secondaryXAxis = $axis;

        return $this;
    }

    /**
     * Get Major Gridlines.
     *
     * @return GridLines
     */
    public function getMajorGridlines()
    {
        if ($this->majorGridlines === null) {
            $this->majorGridlines = new GridLines();
        }

        return $this->majorGridlines;
    }

    /**
     * Get Minor Gridlines.
     *
     * @return GridLines
     */
    public function getMinorGridlines()
    {
        if ($this->minorGridlines === null) {
            $this->minorGridlines = new GridLines();
        }

        return $this->minorGridlines;
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

    /**
     * Render the chart to given file (or stream).
     *
     * @param string $outputDestination Name of the file render to
     *
     * @return bool true on success
     */
    public function render($outputDestination = null)
    {
        if ($outputDestination == 'php://output') {
            $outputDestination = null;
        }

        $libraryName = Settings::getChartRenderer();
        if ($libraryName === null) {
            return false;
        }

        // Ensure that data series values are up-to-date before we render
        $this->refresh();

        $renderer = new $libraryName($this);

        return $renderer->render($outputDestination);
    }
}
