<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Sparkline;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;

/**
 * A group of sparklines that share formatting (type, colours, markers, axis).
 *
 * In OOXML this is the `x14:sparklineGroup` element. A group contains one or
 * more {@see Sparkline} objects (`x14:sparkline`), each pairing a data range
 * with the cell it is drawn in.
 */
class SparklineGroup
{
    // displayEmptyCellsAs values.
    public const EMPTY_AS_GAP = 'gap';
    public const EMPTY_AS_ZERO = 'zero';
    public const EMPTY_AS_SPAN = 'span';

    // Axis min/max scaling type values (minAxisType / maxAxisType).
    public const AXIS_INDIVIDUAL = 'individual';
    public const AXIS_GROUP = 'group';
    public const AXIS_CUSTOM = 'custom';

    private SparklineType $type = SparklineType::Line;

    /**
     * The sparklines belonging to this group.
     *
     * @var Sparkline[]
     */
    private array $sparklines = [];

    private float $lineWeight = 0.75;

    private bool $displayMarkers = false;

    private bool $displayHigh = false;

    private bool $displayLow = false;

    private bool $displayFirst = false;

    private bool $displayLast = false;

    private bool $displayNegative = false;

    private bool $displayXAxis = false;

    private bool $displayHidden = false;

    private bool $rightToLeft = false;

    private string $displayEmptyCellsAs = self::EMPTY_AS_GAP;

    private string $minAxisType = self::AXIS_INDIVIDUAL;

    private string $maxAxisType = self::AXIS_INDIVIDUAL;

    private ?float $manualMin = null;

    private ?float $manualMax = null;

    // Colours are stored as ARGB hex strings (e.g. 'FF376092'), or null when unset.
    private ?string $colorSeries = 'FF376092';

    private ?string $colorNegative = 'FFD00000';

    private ?string $colorAxis = 'FF000000';

    private ?string $colorMarkers = 'FFD00000';

    private ?string $colorFirst = 'FFD00000';

    private ?string $colorLast = 'FFD00000';

    private ?string $colorHigh = 'FFD00000';

    private ?string $colorLow = 'FFD00000';

    public function getType(): SparklineType
    {
        return $this->type;
    }

    public function setType(SparklineType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Sparkline[]
     */
    public function getSparklines(): array
    {
        return $this->sparklines;
    }

    public function addSparkline(Sparkline $sparkline): self
    {
        $this->sparklines[] = $sparkline;

        return $this;
    }

    /**
     * Convenience helper: create and add a sparkline from a location cell and
     * data range, returning this group for chaining.
     */
    public function createSparkline(CellAddress|string $location, string $dataRange): self
    {
        return $this->addSparkline(new Sparkline($location, $dataRange));
    }

    /**
     * @param Sparkline[] $sparklines
     */
    public function setSparklines(array $sparklines): self
    {
        $this->sparklines = array_values($sparklines);

        return $this;
    }

    public function getLineWeight(): float
    {
        return $this->lineWeight;
    }

    public function setLineWeight(float $lineWeight): self
    {
        $this->lineWeight = $lineWeight;

        return $this;
    }

    public function getDisplayMarkers(): bool
    {
        return $this->displayMarkers;
    }

    public function setDisplayMarkers(bool $displayMarkers): self
    {
        $this->displayMarkers = $displayMarkers;

        return $this;
    }

    public function getDisplayHigh(): bool
    {
        return $this->displayHigh;
    }

    public function setDisplayHigh(bool $displayHigh): self
    {
        $this->displayHigh = $displayHigh;

        return $this;
    }

    public function getDisplayLow(): bool
    {
        return $this->displayLow;
    }

    public function setDisplayLow(bool $displayLow): self
    {
        $this->displayLow = $displayLow;

        return $this;
    }

    public function getDisplayFirst(): bool
    {
        return $this->displayFirst;
    }

    public function setDisplayFirst(bool $displayFirst): self
    {
        $this->displayFirst = $displayFirst;

        return $this;
    }

    public function getDisplayLast(): bool
    {
        return $this->displayLast;
    }

    public function setDisplayLast(bool $displayLast): self
    {
        $this->displayLast = $displayLast;

        return $this;
    }

    public function getDisplayNegative(): bool
    {
        return $this->displayNegative;
    }

    public function setDisplayNegative(bool $displayNegative): self
    {
        $this->displayNegative = $displayNegative;

        return $this;
    }

    public function getDisplayXAxis(): bool
    {
        return $this->displayXAxis;
    }

    public function setDisplayXAxis(bool $displayXAxis): self
    {
        $this->displayXAxis = $displayXAxis;

        return $this;
    }

    public function getDisplayHidden(): bool
    {
        return $this->displayHidden;
    }

    public function setDisplayHidden(bool $displayHidden): self
    {
        $this->displayHidden = $displayHidden;

        return $this;
    }

    public function getRightToLeft(): bool
    {
        return $this->rightToLeft;
    }

    public function setRightToLeft(bool $rightToLeft): self
    {
        $this->rightToLeft = $rightToLeft;

        return $this;
    }

    public function getDisplayEmptyCellsAs(): string
    {
        return $this->displayEmptyCellsAs;
    }

    public function setDisplayEmptyCellsAs(string $displayEmptyCellsAs): self
    {
        $this->displayEmptyCellsAs = $displayEmptyCellsAs;

        return $this;
    }

    public function getMinAxisType(): string
    {
        return $this->minAxisType;
    }

    public function setMinAxisType(string $minAxisType): self
    {
        $this->minAxisType = $minAxisType;

        return $this;
    }

    public function getMaxAxisType(): string
    {
        return $this->maxAxisType;
    }

    public function setMaxAxisType(string $maxAxisType): self
    {
        $this->maxAxisType = $maxAxisType;

        return $this;
    }

    public function getManualMin(): ?float
    {
        return $this->manualMin;
    }

    public function setManualMin(?float $manualMin): self
    {
        $this->manualMin = $manualMin;

        return $this;
    }

    public function getManualMax(): ?float
    {
        return $this->manualMax;
    }

    public function setManualMax(?float $manualMax): self
    {
        $this->manualMax = $manualMax;

        return $this;
    }

    public function getColorSeries(): ?string
    {
        return $this->colorSeries;
    }

    public function setColorSeries(?string $colorSeries): self
    {
        $this->colorSeries = $colorSeries;

        return $this;
    }

    public function getColorNegative(): ?string
    {
        return $this->colorNegative;
    }

    public function setColorNegative(?string $colorNegative): self
    {
        $this->colorNegative = $colorNegative;

        return $this;
    }

    public function getColorAxis(): ?string
    {
        return $this->colorAxis;
    }

    public function setColorAxis(?string $colorAxis): self
    {
        $this->colorAxis = $colorAxis;

        return $this;
    }

    public function getColorMarkers(): ?string
    {
        return $this->colorMarkers;
    }

    public function setColorMarkers(?string $colorMarkers): self
    {
        $this->colorMarkers = $colorMarkers;

        return $this;
    }

    public function getColorFirst(): ?string
    {
        return $this->colorFirst;
    }

    public function setColorFirst(?string $colorFirst): self
    {
        $this->colorFirst = $colorFirst;

        return $this;
    }

    public function getColorLast(): ?string
    {
        return $this->colorLast;
    }

    public function setColorLast(?string $colorLast): self
    {
        $this->colorLast = $colorLast;

        return $this;
    }

    public function getColorHigh(): ?string
    {
        return $this->colorHigh;
    }

    public function setColorHigh(?string $colorHigh): self
    {
        $this->colorHigh = $colorHigh;

        return $this;
    }

    public function getColorLow(): ?string
    {
        return $this->colorLow;
    }

    public function setColorLow(?string $colorLow): self
    {
        $this->colorLow = $colorLow;

        return $this;
    }
}
