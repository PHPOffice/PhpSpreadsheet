<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

class Hyperlink
{
    /**
     * URL to link the cell to.
     *
     * @var string
     */
    private $url;

    /**
     * Tooltip to display on the hyperlink.
     *
     * @var string
     */
    private $tooltip;

    /**
     * Create a new Hyperlink.
     *
     * @param string $url Url to link the cell to
     * @param string $tooltip Tooltip to display on the hyperlink
     */
    public function __construct($url = '', $tooltip = '')
    {
        // Initialise member variables
        $this->url = $url;
        $this->tooltip = $tooltip;
    }

    /**
     * Get URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get tooltip.
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * Set tooltip.
     *
     * @param string $tooltip
     *
     * @return $this
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * Is this hyperlink internal? (to another worksheet).
     *
     * @return bool
     */
    public function isInternal()
    {
        return strpos($this->url, 'sheet://') !== false;
    }

    /**
     * @return string
     */
    public function getTypeHyperlink()
    {
        return $this->isInternal() ? '' : 'External';
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->url .
            $this->tooltip .
            __CLASS__
        );
    }
}
