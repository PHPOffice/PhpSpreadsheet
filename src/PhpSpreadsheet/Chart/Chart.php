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
     * Y-Axis Label.
     *
     * @var Title
     */
    private $yAxisLabel;

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
    private $displayBlanksAs = DataSeries::EMPTY_AS_GAP;

    /**
     * Chart Asix Y as.
     *
     * @var Axis
     */
    private $yAxis;

    /**
     * Chart Asix X as.
     *
     * @var Axis
     */
    private $xAxis;

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
    private $bottomRightCellRef = '';

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

    /** @var ?int */
    private $rotX;

    /** @var ?int */
    private $rotY;

    /** @var ?int */
    private $rAngAx;

    /** @var ?int */
    private $perspective;

    /** @var bool */
    private $oneCellAnchor = false;

    /**
     * Create a new Chart.
     *
     * @param mixed $name
     * @param mixed $plotVisibleOnly
     * @param string $displayBlanksAs
     */
    public function __construct($name, ?Title $title = null, ?Legend $legend = null, ?PlotArea $plotArea = null, $plotVisibleOnly = true, $displayBlanksAs = DataSeries::EMPTY_AS_GAP, ?Title $xAxisLabel = null, ?Title $yAxisLabel = null, ?Axis $xAxis = null, ?Axis $yAxis = null, ?GridLines $majorGridlines = null, ?GridLines $minorGridlines = null)
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
     * @return $this
     */
    public function setWorksheet(?Worksheet $worksheet = null)
    {
        $this->worksheet = $worksheet;

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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setYAxisLabel(Title $label)
    {
        $this->yAxisLabel = $label;

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
     * @return $this
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
     * @return $this
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
        if ($this->yAxis !== null) {
            return $this->yAxis;
        }
        $this->yAxis = new Axis();

        return $this->yAxis;
    }

    /**
     * Get xAxis.
     *
     * @return Axis
     */
    public function getChartAxisX()
    {
        if ($this->xAxis !== null) {
            return $this->xAxis;
        }
        $this->xAxis = new Axis();

        return $this->xAxis;
    }

    /**
     * Get Major Gridlines.
     *
     * @return GridLines
     */
    public function getMajorGridlines()
    {
        if ($this->majorGridlines !== null) {
            return $this->majorGridlines;
        }

        return new GridLines();
    }

    /**
     * Get Minor Gridlines.
     *
     * @return GridLines
     */
    public function getMinorGridlines()
    {
        if ($this->minorGridlines !== null) {
            return $this->minorGridlines;
        }

        return new GridLines();
    }

    /**
     * Set the Top Left position for the chart.
     *
     * @param string $cellAddress
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return $this
     */
    public function setTopLeftPosition($cellAddress, $xOffset = null, $yOffset = null)
    {
        $this->topLeftCellRef = $cellAddress;
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
     * @return array{cell: string, xOffset: int, yOffset: int} an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
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
     * @param string $cellAddress
     *
     * @return $this
     */
    public function setTopLeftCell($cellAddress)
    {
        $this->topLeftCellRef = $cellAddress;

        return $this;
    }

    /**
     * Set the offset position within the Top Left cell for the chart.
     *
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return $this
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

    /**
     * @param int $xOffset
     *
     * @return $this
     */
    public function setTopLeftXOffset($xOffset)
    {
        $this->topLeftXOffset = $xOffset;

        return $this;
    }

    public function getTopLeftXOffset(): int
    {
        return $this->topLeftXOffset;
    }

    /**
     * @param int $yOffset
     *
     * @return $this
     */
    public function setTopLeftYOffset($yOffset)
    {
        $this->topLeftYOffset = $yOffset;

        return $this;
    }

    public function getTopLeftYOffset(): int
    {
        return $this->topLeftYOffset;
    }

    /**
     * Set the Bottom Right position of the chart.
     *
     * @param string $cellAddress
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return $this
     */
    public function setBottomRightPosition($cellAddress = '', $xOffset = null, $yOffset = null)
    {
        $this->bottomRightCellRef = $cellAddress;
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

    /**
     * Set the Bottom Right cell for the chart.
     *
     * @return $this
     */
    public function setBottomRightCell(string $cellAddress = '')
    {
        $this->bottomRightCellRef = $cellAddress;

        return $this;
    }

    /**
     * Get the cell address where the bottom right of the chart is fixed.
     */
    public function getBottomRightCell(): string
    {
        return $this->bottomRightCellRef;
    }

    /**
     * Set the offset position within the Bottom Right cell for the chart.
     *
     * @param int $xOffset
     * @param int $yOffset
     *
     * @return $this
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

    /**
     * @param int $xOffset
     *
     * @return $this
     */
    public function setBottomRightXOffset($xOffset)
    {
        $this->bottomRightXOffset = $xOffset;

        return $this;
    }

    public function getBottomRightXOffset(): int
    {
        return $this->bottomRightXOffset;
    }

    /**
     * @param int $yOffset
     *
     * @return $this
     */
    public function setBottomRightYOffset($yOffset)
    {
        $this->bottomRightYOffset = $yOffset;

        return $this;
    }

    public function getBottomRightYOffset(): int
    {
        return $this->bottomRightYOffset;
    }

    public function refresh(): void
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

    public function getRotX(): ?int
    {
        return $this->rotX;
    }

    public function setRotX(?int $rotX): self
    {
        $this->rotX = $rotX;

        return $this;
    }

    public function getRotY(): ?int
    {
        return $this->rotY;
    }

    public function setRotY(?int $rotY): self
    {
        $this->rotY = $rotY;

        return $this;
    }

    public function getRAngAx(): ?int
    {
        return $this->rAngAx;
    }

    public function setRAngAx(?int $rAngAx): self
    {
        $this->rAngAx = $rAngAx;

        return $this;
    }

    public function getPerspective(): ?int
    {
        return $this->perspective;
    }

    public function setPerspective(?int $perspective): self
    {
        $this->perspective = $perspective;

        return $this;
    }

    public function getOneCellAnchor(): bool
    {
        return $this->oneCellAnchor;
    }

    public function setOneCellAnchor(bool $oneCellAnchor): self
    {
        $this->oneCellAnchor = $oneCellAnchor;

        return $this;
    }
}
