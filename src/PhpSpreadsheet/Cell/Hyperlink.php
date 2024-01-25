<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class Hyperlink
{
    /**
     * Create a new Hyperlink.
     *
     * @param string $url Url to link the cell to
     * @param string $tooltip Tooltip to display on the hyperlink
     */
    public function __construct(private string $url = '', private string $tooltip = '')
    {
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
     * Is this hyperlink internal? (to another worksheet).
     */
    public function isInternal(): bool
    {
        return str_contains($this->url, 'sheet://');
    }

    public function getTypeHyperlink(): string
    {
        return $this->isInternal() ? '' : 'External';
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
            . $this->tooltip
            . self::class
        );
    }
}
