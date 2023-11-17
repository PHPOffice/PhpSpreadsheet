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
     */
    private ?Worksheet $worksheet = null;

    /**
     * Chart Title.
     */
    private ?Title $title;

    /**
     * Chart Legend.
     */
    private ?Legend $legend;

    /**
     * X-Axis Label.
     */
    private ?Title $xAxisLabel;

    /**
     * Y-Axis Label.
     */
    private ?Title $yAxisLabel;

    /**
     * Chart Plot Area.
     */
    private ?PlotArea $plotArea;

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
     */
    private Axis $yAxis;

    /**
     * Chart Asix X as.
     */
    private Axis $xAxis;

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
     */
    private string $bottomRightCellRef = '';

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

    private ?int $rotX = null;

    private ?int $rotY = null;

    private ?int $rAngAx = null;

    private ?int $perspective = null;

    /** @var bool */
    private $oneCellAnchor = false;

    /** @var bool */
    private $autoTitleDeleted = false;

    /** @var bool */
    private $noFill = false;

    /** @var bool */
    private $roundedCorners = false;

    private GridLines $borderLines;

    private ChartColor $fillColor;

    /**
     * Rendered width in pixels.
     */
    private ?float $renderedWidth = null;

    /**
     * Rendered height in pixels.
     */
    private ?float $renderedHeight = null;

    /**
     * Create a new Chart.
     * majorGridlines and minorGridlines are deprecated, moved to Axis.
     *
     * @param string $displayBlanksAs
     */
    public function __construct(mixed $name, ?Title $title = null, ?Legend $legend = null, ?PlotArea $plotArea = null, mixed $plotVisibleOnly = true, $displayBlanksAs = DataSeries::EMPTY_AS_GAP, ?Title $xAxisLabel = null, ?Title $yAxisLabel = null, ?Axis $xAxis = null, ?Axis $yAxis = null, ?GridLines $majorGridlines = null, ?GridLines $minorGridlines = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->legend = $legend;
        $this->xAxisLabel = $xAxisLabel;
        $this->yAxisLabel = $yAxisLabel;
        $this->plotArea = $plotArea;
        $this->plotVisibleOnly = $plotVisibleOnly;
        $this->displayBlanksAs = $displayBlanksAs;
        $this->xAxis = $xAxis ?? new Axis();
        $this->yAxis = $yAxis ?? new Axis();
        if ($majorGridlines !== null) {
            $this->yAxis->setMajorGridlines($majorGridlines);
        }
        if ($minorGridlines !== null) {
            $this->yAxis->setMinorGridlines($minorGridlines);
        }
        $this->fillColor = new ChartColor();
        $this->borderLines = new GridLines();
    }

    public function __destruct()
    {
        $this->worksheet = null;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Worksheet.
     */
    public function getWorksheet(): ?Worksheet
    {
        return $this->worksheet;
    }

    /**
     * Set Worksheet.
     *
     * @return $this
     */
    public function setWorksheet(?Worksheet $worksheet = null): static
    {
        $this->worksheet = $worksheet;

        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    /**
     * Set Title.
     *
     * @return $this
     */
    public function setTitle(Title $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLegend(): ?Legend
    {
        return $this->legend;
    }

    /**
     * Set Legend.
     *
     * @return $this
     */
    public function setLegend(Legend $legend): static
    {
        $this->legend = $legend;

        return $this;
    }

    public function getXAxisLabel(): ?Title
    {
        return $this->xAxisLabel;
    }

    /**
     * Set X-Axis Label.
     *
     * @return $this
     */
    public function setXAxisLabel(Title $label): static
    {
        $this->xAxisLabel = $label;

        return $this;
    }

    public function getYAxisLabel(): ?Title
    {
        return $this->yAxisLabel;
    }

    /**
     * Set Y-Axis Label.
     *
     * @return $this
     */
    public function setYAxisLabel(Title $label): static
    {
        $this->yAxisLabel = $label;

        return $this;
    }

    public function getPlotArea(): ?PlotArea
    {
        return $this->plotArea;
    }

    /**
     * Set Plot Area.
     */
    public function setPlotArea(PlotArea $plotArea): self
    {
        $this->plotArea = $plotArea;

        return $this;
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
    public function setPlotVisibleOnly($plotVisibleOnly): static
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
    public function setDisplayBlanksAs($displayBlanksAs): static
    {
        $this->displayBlanksAs = $displayBlanksAs;

        return $this;
    }

    public function getChartAxisY(): Axis
    {
        return $this->yAxis;
    }

    /**
     * Set yAxis.
     */
    public function setChartAxisY(?Axis $axis): self
    {
        $this->yAxis = $axis ?? new Axis();

        return $this;
    }

    public function getChartAxisX(): Axis
    {
        return $this->xAxis;
    }

    /**
     * Set xAxis.
     */
    public function setChartAxisX(?Axis $axis): self
    {
        $this->xAxis = $axis ?? new Axis();

        return $this;
    }

    /**
     * Get Major Gridlines.
     *
     * @deprecated 1.24.0 Use Axis->getMajorGridlines()
     * @see Axis::getMajorGridlines()
     *
     * @codeCoverageIgnore
     */
    public function getMajorGridlines(): ?GridLines
    {
        return $this->yAxis->getMajorGridLines();
    }

    /**
     * Get Minor Gridlines.
     *
     * @deprecated 1.24.0 Use Axis->getMinorGridlines()
     * @see Axis::getMinorGridlines()
     *
     * @codeCoverageIgnore
     */
    public function getMinorGridlines(): ?GridLines
    {
        return $this->yAxis->getMinorGridLines();
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
    public function setTopLeftPosition($cellAddress, $xOffset = null, $yOffset = null): static
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
     * Returns ['cell' => string cell address, 'xOffset' => int, 'yOffset' => int].
     *
     * @return array{cell: string, xOffset: int, yOffset: int} an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
     */
    public function getTopLeftPosition(): array
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
    public function setTopLeftCell($cellAddress): static
    {
        $this->topLeftCellRef = $cellAddress;

        return $this;
    }

    /**
     * Set the offset position within the Top Left cell for the chart.
     *
     * @param ?int $xOffset
     * @param ?int $yOffset
     *
     * @return $this
     */
    public function setTopLeftOffset($xOffset, $yOffset): static
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
    public function getTopLeftOffset(): array
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
    public function setTopLeftXOffset($xOffset): static
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
    public function setTopLeftYOffset($yOffset): static
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
    public function setBottomRightPosition($cellAddress = '', $xOffset = null, $yOffset = null): static
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
    public function getBottomRightPosition(): array
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
    public function setBottomRightCell(string $cellAddress = ''): static
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
     * @param ?int $xOffset
     * @param ?int $yOffset
     *
     * @return $this
     */
    public function setBottomRightOffset($xOffset, $yOffset): static
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
    public function getBottomRightOffset(): array
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
    public function setBottomRightXOffset($xOffset): static
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
    public function setBottomRightYOffset($yOffset): static
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
        if ($this->worksheet !== null && $this->plotArea !== null) {
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

        /** @var Renderer\IRenderer */
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

    public function getAutoTitleDeleted(): bool
    {
        return $this->autoTitleDeleted;
    }

    public function setAutoTitleDeleted(bool $autoTitleDeleted): self
    {
        $this->autoTitleDeleted = $autoTitleDeleted;

        return $this;
    }

    public function getNoFill(): bool
    {
        return $this->noFill;
    }

    public function setNoFill(bool $noFill): self
    {
        $this->noFill = $noFill;

        return $this;
    }

    public function getRoundedCorners(): bool
    {
        return $this->roundedCorners;
    }

    public function setRoundedCorners(?bool $roundedCorners): self
    {
        if ($roundedCorners !== null) {
            $this->roundedCorners = $roundedCorners;
        }

        return $this;
    }

    public function getBorderLines(): GridLines
    {
        return $this->borderLines;
    }

    public function setBorderLines(GridLines $borderLines): self
    {
        $this->borderLines = $borderLines;

        return $this;
    }

    public function getFillColor(): ChartColor
    {
        return $this->fillColor;
    }

    public function setRenderedWidth(?float $width): self
    {
        $this->renderedWidth = $width;

        return $this;
    }

    public function getRenderedWidth(): ?float
    {
        return $this->renderedWidth;
    }

    public function setRenderedHeight(?float $height): self
    {
        $this->renderedHeight = $height;

        return $this;
    }

    public function getRenderedHeight(): ?float
    {
        return $this->renderedHeight;
    }
}
