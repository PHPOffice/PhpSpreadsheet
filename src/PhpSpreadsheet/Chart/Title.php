<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Title
{
    /**
     * Title Caption.
     *
     * @var array|RichText|string
     */
    private $caption = '';

    /**
     * Title Layout.
     *
     * @var Layout
     */
    private $layout;

    /**
     * Create a new Title.
     *
     * @param array|RichText|string $caption
     */
    public function __construct($caption = '', ?Layout $layout = null)
    {
        $this->caption = $caption;
        $this->layout = $layout;
    }

    /**
     * Get caption.
     *
     * @return array|RichText|string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    public function getCaptionText(): string
    {
        $caption = $this->caption;
        if (is_string($caption)) {
            return $caption;
        }
        if ($caption instanceof RichText) {
            return $caption->getPlainText();
        }
        $retVal = '';
        foreach ($caption as $textx) {
            /** @var RichText|string */
            $text = $textx;
            if ($text instanceof RichText) {
                $retVal .= $text->getPlainText();
            } else {
                $retVal .= $text;
            }
        }

        return $retVal;
    }

    /**
     * Set caption.
     *
     * @param array|RichText|string $caption
     *
     * @return $this
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get Layout.
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
