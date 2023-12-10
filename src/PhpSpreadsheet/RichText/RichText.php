<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IComparable;
use Stringable;

class RichText implements IComparable, Stringable
{
    /**
     * Rich text elements.
     *
     * @var ITextElement[]
     */
    private array $richTextElements;

    /**
     * Create a new RichText instance.
     */
    public function __construct(?Cell $cell = null)
    {
        // Initialise variables
        $this->richTextElements = [];

        // Rich-Text string attached to cell?
        if ($cell !== null) {
            // Add cell text and style
            if ($cell->getValue() != '') {
                $objRun = new Run($cell->getValue());
                $objRun->setFont(clone $cell->getWorksheet()->getStyle($cell->getCoordinate())->getFont());
                $this->addText($objRun);
            }

            // Set parent value
            $cell->setValueExplicit($this, DataType::TYPE_STRING);
        }
    }

    /**
     * Add text.
     *
     * @param ITextElement $text Rich text element
     *
     * @return $this
     */
    public function addText(ITextElement $text): static
    {
        $this->richTextElements[] = $text;

        return $this;
    }

    /**
     * Create text.
     *
     * @param string $text Text
     */
    public function createText(string $text): TextElement
    {
        $objText = new TextElement($text);
        $this->addText($objText);

        return $objText;
    }

    /**
     * Create text run.
     *
     * @param string $text Text
     */
    public function createTextRun(string $text): Run
    {
        $objText = new Run($text);
        $this->addText($objText);

        return $objText;
    }

    /**
     * Get plain text.
     */
    public function getPlainText(): string
    {
        // Return value
        $returnValue = '';

        // Loop through all ITextElements
        foreach ($this->richTextElements as $text) {
            $returnValue .= $text->getText();
        }

        return $returnValue;
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->getPlainText();
    }

    /**
     * Get Rich Text elements.
     *
     * @return ITextElement[]
     */
    public function getRichTextElements(): array
    {
        return $this->richTextElements;
    }

    /**
     * Set Rich Text elements.
     *
     * @param ITextElement[] $textElements Array of elements
     *
     * @return $this
     */
    public function setRichTextElements(array $textElements): static
    {
        $this->richTextElements = $textElements;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        $hashElements = '';
        foreach ($this->richTextElements as $element) {
            $hashElements .= $element->getHashCode();
        }

        return md5(
            $hashElements
            . __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            $newValue = is_object($value) ? (clone $value) : $value;
            if (is_array($value)) {
                $newValue = [];
                foreach ($value as $key2 => $value2) {
                    $newValue[$key2] = is_object($value2) ? (clone $value2) : $value2;
                }
            }
            $this->$key = $newValue;
        }
    }
}
