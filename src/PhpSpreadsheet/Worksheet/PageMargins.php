<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class PageMargins
{
    /**
     * Left.
     *
     * @var float
     */
    private $left = 0.7;

    /**
     * Right.
     *
     * @var float
     */
    private $right = 0.7;

    /**
     * Top.
     *
     * @var float
     */
    private $top = 0.75;

    /**
     * Bottom.
     *
     * @var float
     */
    private $bottom = 0.75;

    /**
     * Header.
     *
     * @var float
     */
    private $header = 0.3;

    /**
     * Footer.
     *
     * @var float
     */
    private $footer = 0.3;

    /**
     * Create a new PageMargins.
     */
    public function __construct()
    {
    }

    /**
     * Get Left.
     *
     * @return float
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set Left.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setLeft($pValue)
    {
        $this->left = $pValue;

        return $this;
    }

    /**
     * Get Right.
     *
     * @return float
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set Right.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setRight($pValue)
    {
        $this->right = $pValue;

        return $this;
    }

    /**
     * Get Top.
     *
     * @return float
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Set Top.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setTop($pValue)
    {
        $this->top = $pValue;

        return $this;
    }

    /**
     * Get Bottom.
     *
     * @return float
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * Set Bottom.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setBottom($pValue)
    {
        $this->bottom = $pValue;

        return $this;
    }

    /**
     * Get Header.
     *
     * @return float
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set Header.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setHeader($pValue)
    {
        $this->header = $pValue;

        return $this;
    }

    /**
     * Get Footer.
     *
     * @return float
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set Footer.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setFooter($pValue)
    {
        $this->footer = $pValue;

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
