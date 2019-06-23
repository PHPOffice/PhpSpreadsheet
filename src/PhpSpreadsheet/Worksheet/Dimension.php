<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

abstract class Dimension
{
    /**
     * Visible?
     *
     * @var bool
     */
    private $visible = true;

    /**
     * Outline level.
     *
     * @var int
     */
    private $outlineLevel = 0;

    /**
     * Collapsed.
     *
     * @var bool
     */
    private $collapsed = false;

    /**
     * Index to cellXf. Null value means row has no explicit cellXf format.
     *
     * @var null|int
     */
    private $xfIndex;

    /**
     * Create a new Dimension.
     *
     * @param int $initialValue Numeric row index
     */
    public function __construct($initialValue = null)
    {
        // set dimension as unformatted by default
        $this->xfIndex = $initialValue;
    }

    /**
     * Get Visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set Visible.
     *
     * @param bool $pValue
     *
     * @return Dimension
     */
    public function setVisible($pValue)
    {
        $this->visible = (bool) $pValue;

        return $this;
    }

    /**
     * Get Outline Level.
     *
     * @return int
     */
    public function getOutlineLevel()
    {
        return $this->outlineLevel;
    }

    /**
     * Set Outline Level.
     * Value must be between 0 and 7.
     *
     * @param int $pValue
     *
     * @throws PhpSpreadsheetException
     *
     * @return Dimension
     */
    public function setOutlineLevel($pValue)
    {
        if ($pValue < 0 || $pValue > 7) {
            throw new PhpSpreadsheetException('Outline level must range between 0 and 7.');
        }

        $this->outlineLevel = $pValue;

        return $this;
    }

    /**
     * Get Collapsed.
     *
     * @return bool
     */
    public function getCollapsed()
    {
        return $this->collapsed;
    }

    /**
     * Set Collapsed.
     *
     * @param bool $pValue
     *
     * @return Dimension
     */
    public function setCollapsed($pValue)
    {
        $this->collapsed = (bool) $pValue;

        return $this;
    }

    /**
     * Get index to cellXf.
     *
     * @return int
     */
    public function getXfIndex()
    {
        return $this->xfIndex;
    }

    /**
     * Set index to cellXf.
     *
     * @param int $pValue
     *
     * @return Dimension
     */
    public function setXfIndex($pValue)
    {
        $this->xfIndex = $pValue;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
