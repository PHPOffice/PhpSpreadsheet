<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Style\Font;

class Run extends TextElement implements ITextElement
{
    /**
     * Font.
     *
     * @var Font
     */
    private $font;

    /**
     * Create a new Run instance.
     *
     * @param string $pText Text
     */
    public function __construct($pText = '')
    {
        parent::__construct($pText);
        // Initialise variables
        $this->font = new Font();
    }

    /**
     * Get font.
     *
     * @return Font
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set font.
     *
     * @param Font $pFont Font
     *
     * @return ITextElement
     */
    public function setFont(Font $pFont = null)
    {
        $this->font = $pFont;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->getText() .
            $this->font->getHashCode() .
            __CLASS__
        );
    }
}
