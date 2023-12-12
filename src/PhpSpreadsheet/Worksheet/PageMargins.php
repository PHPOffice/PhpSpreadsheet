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
     * @param float $left
     *
     * @return $this
     */
    public function setLeft($left): static
    {
        $this->left = $left;

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
     * @param float $right
     *
     * @return $this
     */
    public function setRight($right): static
    {
        $this->right = $right;

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
     * @param float $top
     *
     * @return $this
     */
    public function setTop($top): static
    {
        $this->top = $top;

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
     * @param float $bottom
     *
     * @return $this
     */
    public function setBottom($bottom): static
    {
        $this->bottom = $bottom;

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
     * @param float $header
     *
     * @return $this
     */
    public function setHeader($header): static
    {
        $this->header = $header;

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
     * @param float $footer
     *
     * @return $this
     */
    public function setFooter($footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    public static function fromCentimeters(float $value): float
    {
        return $value / 2.54;
    }

    public static function toCentimeters(float $value): float
    {
        return $value * 2.54;
    }

    public static function fromMillimeters(float $value): float
    {
        return $value / 25.4;
    }

    public static function toMillimeters(float $value): float
    {
        return $value * 25.4;
    }

    public static function fromPoints(float $value): float
    {
        return $value / 72;
    }

    public static function toPoints(float $value): float
    {
        return $value * 72;
    }
}
