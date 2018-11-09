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
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set Row Index.
     *
     * @param int $pValue
     *
     * @return RowDimension
     */
    public function setRowIndex($pValue)
    {
        $this->rowIndex = $pValue;

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
     * @param float $pValue
     *
     * @return RowDimension
     */
    public function setRowHeight($pValue)
    {
        $this->height = $pValue;

        return $this;
    }

    /**
     * Get ZeroHeight.
     *
     * @return bool
     */
    public function getZeroHeight()
    {
        return $this->zeroHeight;
    }

    /**
     * Set ZeroHeight.
     *
     * @param bool $pValue
     *
     * @return RowDimension
     */
    public function setZeroHeight($pValue)
    {
        $this->zeroHeight = $pValue;

        return $this;
    }
}
