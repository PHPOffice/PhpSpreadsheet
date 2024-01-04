<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Run extends TextElement implements ITextElement
{
    /**
     * Font.
     *
     * @var ?Font
     */
    private ?Font $font;

    /**
     * Create a new Run instance.
     *
     * @param string $text Text
     */
    public function __construct(string $text = '')
    {
        parent::__construct($text);
        // Initialise variables
        $this->font = new Font();
    }

    /**
     * Get font.
     */
    public function getFont(): ?Font
    {
        return $this->font;
    }

    public function getFontOrThrow(): Font
    {
        if ($this->font === null) {
            throw new SpreadsheetException('unexpected null font');
        }

        return $this->font;
    }

    /**
     * Set font.
     *
     * @param ?Font $font Font
     *
     * @return $this
     */
    public function setFont(?Font $font = null): static
    {
        $this->font = $font;

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
            $this->getText()
            . (($this->font === null) ? '' : $this->font->getHashCode())
            . __CLASS__
        );
    }
}
