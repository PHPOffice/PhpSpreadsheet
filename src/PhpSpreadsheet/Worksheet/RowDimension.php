<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Helper\Dimension as CssDimension;

class RowDimension extends Dimension
{
    private ?int $rowIndex;

    /**
     * Row height (in pt).
     *
     * When this is set to a negative value, the row height should be ignored by IWriter
     */
    private float $height = -1;

    /**
     * ZeroHeight for Row?
     */
    private bool $zeroHeight = false;

    private bool $customFormat = false;

    private bool $visibleAfterFilter = true;

    public function setVisibleAfterFilter(bool $visibleAfterFilter): self
    {
        $this->visibleAfterFilter = $visibleAfterFilter;

        return $this;
    }

    public function getVisibleAfterFilter(): bool
    {
        return $this->visibleAfterFilter;
    }

    /**
     * @param ?int $index Numeric row index
     */
    public function __construct(?int $index = 0)
    {
        // Initialise values
        $this->rowIndex = $index;

        // set dimension as unformatted by default
        parent::__construct(null);
    }

    public function getRowIndex(): ?int
    {
        return $this->rowIndex;
    }

    public function setRowIndex(int $index): static
    {
        $this->rowIndex = $index;

        return $this;
    }

    /**
     * Get Row Height.
     * By default, this will be in points; but this method also accepts an optional unit of measure
     *    argument, and will convert the value from points to the specified UoM.
     *    A value of -1 tells Excel to display this column in its default height.
     */
    public function getRowHeight(?string $unitOfMeasure = null): float
    {
        return ($unitOfMeasure === null || $this->height < 0)
            ? $this->height
            : (new CssDimension($this->height . CssDimension::UOM_POINTS))->toUnit($unitOfMeasure);
    }

    /**
     * Set Row Height.
     *
     * @param float $height in points. A value of -1 tells Excel to display this column in its default height.
     * By default, this will be the passed argument value; but this method also accepts an optional unit of measure
     *    argument, and will convert the passed argument value to points from the specified UoM
     */
    public function setRowHeight(float $height, ?string $unitOfMeasure = null): static
    {
        $this->height = ($unitOfMeasure === null || $height < 0)
            ? $height
            : (new CssDimension("{$height}{$unitOfMeasure}"))->height();
        $this->customFormat = false;

        return $this;
    }

    public function getZeroHeight(): bool
    {
        return $this->zeroHeight;
    }

    public function setZeroHeight(bool $zeroHeight): static
    {
        $this->zeroHeight = $zeroHeight;

        return $this;
    }

    public function getCustomFormat(): bool
    {
        return $this->customFormat;
    }

    public function setCustomFormat(bool $customFormat, ?float $height = -1): self
    {
        $this->customFormat = $customFormat;
        if ($height !== null) {
            $this->height = $height;
        }

        return $this;
    }
}
