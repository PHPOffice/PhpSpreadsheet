<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

abstract class Dimension
{
    /**
     * Visible?
     */
    private bool $visible = true;

    /**
     * Outline level.
     */
    private int $outlineLevel = 0;

    /**
     * Collapsed.
     */
    private bool $collapsed = false;

    /**
     * Create a new Dimension.
     *
     * @param ?int $xfIndex Numeric row index
     */
    public function __construct(private ?int $xfIndex = null)
    {
    }

    /**
     * Get Visible.
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set Visible.
     *
     * @return $this
     */
    public function setVisible(bool $visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get Outline Level.
     */
    public function getOutlineLevel(): int
    {
        return $this->outlineLevel;
    }

    /**
     * Set Outline Level.
     * Value must be between 0 and 7.
     *
     * @return $this
     */
    public function setOutlineLevel(int $level)
    {
        if ($level < 0 || $level > 7) {
            throw new PhpSpreadsheetException('Outline level must range between 0 and 7.');
        }

        $this->outlineLevel = $level;

        return $this;
    }

    /**
     * Get Collapsed.
     */
    public function getCollapsed(): bool
    {
        return $this->collapsed;
    }

    /**
     * Set Collapsed.
     *
     * @return $this
     */
    public function setCollapsed(bool $collapsed)
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    /**
     * Get index to cellXf.
     */
    public function getXfIndex(): ?int
    {
        return $this->xfIndex;
    }

    /**
     * Set index to cellXf.
     *
     * @return $this
     */
    public function setXfIndex(int $XfIndex)
    {
        $this->xfIndex = $XfIndex;

        return $this;
    }
}
