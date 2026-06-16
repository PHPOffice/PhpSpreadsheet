<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class Hyperlink
{
    /**
     * URL to link the cell to.
     */
    private string $url;

    /**
     * Tooltip to display on the hyperlink.
     */
    private string $tooltip;

    private string $display = '';

    /**
     * Create a new Hyperlink.
     *
     * @param string $url Url to link the cell to
     * @param string $tooltip Tooltip to display on the hyperlink
     */
    public function __construct(string $url = '', string $tooltip = '')
    {
        // Initialise member variables
        $this->url = $url;
        $this->tooltip = $tooltip;
    }

    /**
     * Get URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set URL.
     *
     * @return $this
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get tooltip.
     */
    public function getTooltip(): string
    {
        return $this->tooltip;
    }

    /**
     * Set tooltip.
     *
     * @return $this
     */
    public function setTooltip(string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * Is this hyperlink internal? (to another worksheet or a cell in this worksheet).
     */
    public function isInternal(): bool
    {
        return str_starts_with($this->url, 'sheet://') || str_starts_with($this->url, '#');
    }

    public function getTypeHyperlink(): string
    {
        return $this->isInternal() ? '' : 'External';
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * This can be displayed in cell rather than actual cell contents.
     * It seems to be ignored by Excel.
     * It may be used by Google Sheets.
     */
    public function setDisplay(string $display): self
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            $this->url
            . ','
            . $this->tooltip
            . ','
            . $this->display
            . ','
            . __CLASS__
        );
    }
}
