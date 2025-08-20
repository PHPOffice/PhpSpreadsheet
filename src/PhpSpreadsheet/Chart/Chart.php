<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Chart
{
    /**
     * Chart Name.
     */
    private string $name;

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
     */
    private bool $plotVisibleOnly;

    /**
     * Display Blanks as.
     */
    private string $displayBlanksAs;

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
     */
    private string $topLeftCellRef = 'A1';

    /**
     * Top-Left X-Offset.
     */
    private int $topLeftXOffset = 0;

    /**
     * Top-Left Y-Offset.
     */
    private int $topLeftYOffset = 0;

    /**
     * Bottom-Right Cell Position.
     */
    private string $bottomRightCellRef = '';

    /**
     * Bottom-Right X-Offset.
     */
    private int $bottomRightXOffset = 10;

    /**
     * Bottom-Right Y-Offset.
     */
    private int $bottomRightYOffset = 10;

    private ?int $rotX = null;

    private ?int $rotY = null;

    private ?int $rAngAx = null;

    private ?int $perspective = null;

    private bool $oneCellAnchor = false;

    private bool $autoTitleDeleted = false;

    private bool $noFill = false;

    private bool $noBorder = false;

    private bool $roundedCorners = false;

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
     */
    public function __construct(string $name, ?Title $title = null, ?Legend $legend = null, ?PlotArea $plotArea = null, bool $plotVisibleOnly = true, string $displayBlanksAs = DataSeries::DEFAULT_EMPTY_AS, ?Title $xAxisLabel = null, ?Title $yAxisLabel = null, ?Axis $xAxis = null, ?Axis $yAxis = null, ?GridLines $majorGridlines = null, ?GridLines $minorGridlines = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->legend = $legend;
        $this->xAxisLabel = $xAxisLabel;
        $this->yAxisLabel = $yAxisLabel;
        $this->plotArea = $plotArea;
        $this->plotVisibleOnly = $plotVisibleOnly;
        $this->setDisplayBlanksAs($displayBlanksAs);
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
     */
    public function getName(): string
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

    public function getPlotAreaOrThrow(): PlotArea
    {
        $plotArea = $this->getPlotArea();
        if ($plotArea !== null) {
            return $plotArea;
        }

        throw new Exception('Chart has no PlotArea');
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
     */
    public function getPlotVisibleOnly(): bool
    {
        return $this->plotVisibleOnly;
    }

    /**
     * Set Plot Visible Only.
     *
     * @return $this
     */
    public function setPlotVisibleOnly(bool $plotVisibleOnly): static
    {
        $this->plotVisibleOnly = $plotVisibleOnly;

        return $this;
    }

    /**
     * Get Display Blanks as.
     */
    public function getDisplayBlanksAs(): string
    {
        return $this->displayBlanksAs;
    }

    /**
     * Set Display Blanks as.
     *
     * @return $this
     */
    public function setDisplayBlanksAs(string $displayBlanksAs): static
    {
        $displayBlanksAs = strtolower($displayBlanksAs);
        $this->displayBlanksAs = in_array($displayBlanksAs, DataSeries::VALID_EMPTY_AS, true) ? $displayBlanksAs : DataSeries::DEFAULT_EMPTY_AS;

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
     * Set the Top Left position for the chart.
     *
     * @return $this
     */
    public function setTopLeftPosition(string $cellAddress, ?int $xOffset = null, ?int $yOffset = null): static
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
     */
    public function getTopLeftCell(): string
    {
        return $this->topLeftCellRef;
    }

    /**
     * Set the Top Left cell position for the chart.
     *
     * @return $this
     */
    public function setTopLeftCell(string $cellAddress): static
    {
        $this->topLeftCellRef = $cellAddress;

        return $this;
    }

    /**
     * Set the offset position within the Top Left cell for the chart.
     *
     * @return $this
     */
    public function setTopLeftOffset(?int $xOffset, ?int $yOffset): static
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
     * @return $this
     */
    public function setTopLeftXOffset(int $xOffset): static
    {
        $this->topLeftXOffset = $xOffset;

        return $this;
    }

    public function getTopLeftXOffset(): int
    {
        return $this->topLeftXOffset;
    }

    /**
     * @return $this
     */
    public function setTopLeftYOffset(int $yOffset): static
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
     * @return $this
     */
    public function setBottomRightPosition(string $cellAddress = '', ?int $xOffset = null, ?int $yOffset = null): static
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
     * @return array{cell: string, xOffset: int, yOffset:int} an associative array containing the cell address, X-Offset and Y-Offset from the top left of that cell
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
     * @return $this
     */
    public function setBottomRightOffset(?int $xOffset, ?int $yOffset): static
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
     * @return $this
     */
    public function setBottomRightXOffset(int $xOffset): static
    {
        $this->bottomRightXOffset = $xOffset;

        return $this;
    }

    public function getBottomRightXOffset(): int
    {
        return $this->bottomRightXOffset;
    }

    /**
     * @return $this
     */
    public function setBottomRightYOffset(int $yOffset): static
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
     * @param ?string $outputDestination Name of the file render to
     *
     * @return bool true on success
     */
    public function render(?string $outputDestination = null): bool
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

    public function getNoBorder(): bool
    {
        return $this->noBorder;
    }

    public function setNoBorder(bool $noBorder): self
    {
        $this->noBorder = $noBorder;

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

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $this->worksheet = null;
        $this->title = ($this->title === null) ? null : clone $this->title;
        $this->legend = ($this->legend === null) ? null : clone $this->legend;
        $this->xAxisLabel = ($this->xAxisLabel === null) ? null : clone $this->xAxisLabel;
        $this->yAxisLabel = ($this->yAxisLabel === null) ? null : clone $this->yAxisLabel;
        $this->plotArea = ($this->plotArea === null) ? null : clone $this->plotArea;
        $this->xAxis = clone $this->xAxis;
        $this->yAxis = clone $this->yAxis;
        $this->borderLines = clone $this->borderLines;
        $this->fillColor = clone $this->fillColor;
    }
}
