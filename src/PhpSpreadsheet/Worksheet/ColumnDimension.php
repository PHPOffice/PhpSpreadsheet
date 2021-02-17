<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class ColumnDimension extends Dimension
{
    /**
     * Column index.
     *
     * @var string
     */
    private $columnIndex;

    /**
     * Column width.
     *
     * When this is set to a negative value, the column width should be ignored by IWriter
     *
     * @var float
     */
    private $width = -1;

    /**
     * Auto size?
     *
     * @var bool
     */
    private $autoSize = false;

    /**
     * Create a new ColumnDimension.
     *
     * @param string $pIndex Character column index
     */
    public function __construct($pIndex = 'A')
    {
        // Initialise values
        $this->columnIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(0);
    }

    /**
     * Get column index as string eg: 'A'.
     *
     * @return string
     */
    public function getColumnIndex(): string
    {
        return $this->columnIndex;
    }

    /**
     * Set column index as string eg: 'A'.
     *
     * @param string $index
     *
     * @return $this
     */
    public function setColumnIndex(string $index)
    {
        $this->columnIndex = $index;

        return $this;
    }

    /**
     * Get Width.
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width.
     *
     * @param float $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get Auto Size.
     *
     * @return bool
     */
    public function getAutoSize(): bool
    {
        return $this->autoSize;
    }

    /**
     * Set Auto Size.
     *
     * @param bool $autosizeEnabled
     *
     * @return $this
     */
    public function setAutoSize(bool $autosizeEnabled)
    {
        $this->autoSize = $autosizeEnabled;

        return $this;
    }
}
