<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class RowDimension extends Dimension
{
    /**
     * Row index.
     *
     * @var int
     */
    private $rowIndex;

    /**
     * Row height (in pt).
     *
     * When this is set to a negative value, the row height should be ignored by IWriter
     *
     * @var float
     */
    private $height = -1;

    /**
     * ZeroHeight for Row?
     *
     * @var bool
     */
    private $zeroHeight = false;

    /**
     * Create a new RowDimension.
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($pIndex = 0)
    {
        // Initialise values
        $this->rowIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(null);
    }

    /**
     * Get Row Index.
     */
    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * Set Row Index.
     *
     * @return $this
     */
    public function setRowIndex(int $index)
    {
        $this->rowIndex = $index;

        return $this;
    }

    /**
     * Get Row Height.
     *
     * @return float
     */
    public function getRowHeight()
    {
        return $this->height;
    }

    /**
     * Set Row Height.
     *
     * @param float $height
     *
     * @return $this
     */
    public function setRowHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get ZeroHeight.
     */
    public function getZeroHeight(): bool
    {
        return $this->zeroHeight;
    }

    /**
     * Set ZeroHeight.
     *
     * @return $this
     */
    public function setZeroHeight(bool $pValue)
    {
        $this->zeroHeight = $pValue;

        return $this;
    }
}
