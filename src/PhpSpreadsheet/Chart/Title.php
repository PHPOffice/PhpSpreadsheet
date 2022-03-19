<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;

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
     * Title Font
     * @var Font
     */
    private $font;

    /**
     * Create a new Title.
     *
     * @param array|RichText|string $caption
     */
    public function __construct($caption = '', ?Layout $layout = null)
    {
        $this->caption = $caption;
        $this->layout = $layout;
        $this->font = new Font();
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
    /**
     * Get font
     *
     * @return Font
     */
    public function getFont() {
        return $this->font;
    }

    /**
     * Set font
     *
     * @param Font $font
     * @return Title
     */
    public function setFont(Font $font = null)  {
        $this->font = $font;
        return $this;
    }
}
