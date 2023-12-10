<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Style\Font;

class TextElement implements ITextElement
{
    /**
     * Text.
     */
    private string $text;

    /**
     * Create a new TextElement instance.
     *
     * @param string $text Text
     */
    public function __construct(string $text = '')
    {
        // Initialise variables
        $this->text = $text;
    }

    /**
     * Get text.
     *
     * @return string Text
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set text.
     *
     * @param string $text Text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get font. For this class, the return value is always null.
     */
    public function getFont(): ?Font
    {
        return null;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            $this->text
            . __CLASS__
        );
    }
}
