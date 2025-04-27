<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlotArea
{
    /**
     * No fill in plot area (show Excel gridlines through chart).
     */
    private bool $noFill = false;

    /**
     * PlotArea Gradient Stop list.
     * Each entry is a 2-element array.
     *     First is position in %.
     *     Second is ChartColor.
     *
     * @var array<array{float, ChartColor}>
     */
    private array $gradientFillStops = [];

    /**
     * PlotArea Gradient Angle.
     */
    private ?float $gradientFillAngle = null;

    /**
     * PlotArea Layout.
     */
    private ?Layout $layout;

    /**
     * Plot Series.
     *
     * @var DataSeries[]
     */
    private array $plotSeries;

    /**
     * Create a new PlotArea.
     *
     * @param DataSeries[] $plotSeries
     */
    public function __construct(?Layout $layout = null, array $plotSeries = [])
    {
        $this->layout = $layout;
        $this->plotSeries = $plotSeries;
    }

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    /**
     * Get Number of Plot Groups.
     */
    public function getPlotGroupCount(): int
    {
        return count($this->plotSeries);
    }

    /**
     * Get Number of Plot Series.
     */
    public function getPlotSeriesCount(): int|float
    {
        $seriesCount = 0;
        foreach ($this->plotSeries as $plot) {
            $seriesCount += $plot->getPlotSeriesCount();
        }

        return $seriesCount;
    }

    /**
     * Get Plot Series.
     *
     * @return DataSeries[]
     */
    public function getPlotGroup(): array
    {
        return $this->plotSeries;
    }

    /**
     * Get Plot Series by Index.
     */
    public function getPlotGroupByIndex(int $index): DataSeries
    {
        return $this->plotSeries[$index];
    }

    /**
     * Set Plot Series.
     *
     * @param DataSeries[] $plotSeries
     *
     * @return $this
     */
    public function setPlotSeries(array $plotSeries): static
    {
        $this->plotSeries = $plotSeries;

        return $this;
    }

    public function refresh(Worksheet $worksheet): void
    {
        foreach ($this->plotSeries as $plotSeries) {
            $plotSeries->refresh($worksheet);
        }
    }

    public function setNoFill(bool $noFill): self
    {
        $this->noFill = $noFill;

        return $this;
    }

    public function getNoFill(): bool
    {
        return $this->noFill;
    }

    /** @param array<array{float, ChartColor}> $gradientFillStops */
    public function setGradientFillProperties(array $gradientFillStops, ?float $gradientFillAngle): self
    {
        $this->gradientFillStops = $gradientFillStops;
        $this->gradientFillAngle = $gradientFillAngle;

        return $this;
    }

    /**
     * Get gradientFillAngle.
     */
    public function getGradientFillAngle(): ?float
    {
        return $this->gradientFillAngle;
    }

    /**
     * Get gradientFillStops.
     *
     * @return array<array{float, ChartColor}>
     */
    public function getGradientFillStops(): array
    {
        return $this->gradientFillStops;
    }

    private ?int $gapWidth = null;

    private bool $useUpBars = false;

    private bool $useDownBars = false;

    public function getGapWidth(): ?int
    {
        return $this->gapWidth;
    }

    public function setGapWidth(?int $gapWidth): self
    {
        $this->gapWidth = $gapWidth;

        return $this;
    }

    public function getUseUpBars(): bool
    {
        return $this->useUpBars;
    }

    public function setUseUpBars(bool $useUpBars): self
    {
        $this->useUpBars = $useUpBars;

        return $this;
    }

    public function getUseDownBars(): bool
    {
        return $this->useDownBars;
    }

    public function setUseDownBars(bool $useDownBars): self
    {
        $this->useDownBars = $useDownBars;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $this->layout = ($this->layout === null) ? null : clone $this->layout;
        $plotSeries = $this->plotSeries;
        $this->plotSeries = [];
        foreach ($plotSeries as $series) {
            $this->plotSeries[] = clone $series;
        }
    }
}
