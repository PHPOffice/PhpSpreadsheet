<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IComparable;

class RichText implements IComparable
{
    /**
     * Rich text elements.
     *
     * @var ITextElement[]
     */
    private $richTextElements;

    /**
     * Create a new RichText instance.
     *
     * @param Cell $pCell
     */
    public function __construct(?Cell $pCell = null)
    {
        // Initialise variables
        $this->richTextElements = [];

        // Rich-Text string attached to cell?
        if ($pCell !== null) {
            // Add cell text and style
            if ($pCell->getValue() != '') {
                $objRun = new Run($pCell->getValue());
                $objRun->setFont(clone $pCell->getWorksheet()->getStyle($pCell->getCoordinate())->getFont());
                $this->addText($objRun);
            }

            // Set parent value
            $pCell->setValueExplicit($this, DataType::TYPE_STRING);
        }
    }

    /**
     * Add text.
     *
     * @param ITextElement $pText Rich text element
     *
     * @return $this
     */
    public function addText(ITextElement $pText)
    {
        $this->richTextElements[] = $pText;

        return $this;
    }

    /**
     * Create text.
     *
     * @param string $pText Text
     *
     * @return TextElement
     */
    public function createText($pText)
    {
        $objText = new TextElement($pText);
        $this->addText($objText);

        return $objText;
    }

    /**
     * Create text run.
     *
     * @param string $pText Text
     *
     * @return Run
     */
    public function createTextRun($pText)
    {
        $objText = new Run($pText);
        $this->addText($objText);

        return $objText;
    }

    /**
     * Get plain text.
     *
     * @return string
     */
    public function getPlainText()
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
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getPlainText();
    }

    /**
     * Get Rich Text elements.
     *
     * @return ITextElement[]
     */
    public function getRichTextElements()
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
    public function setRichTextElements(array $textElements)
    {
        $this->richTextElements = $textElements;

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        $hashElements = '';
        foreach ($this->richTextElements as $element) {
            $hashElements .= $element->getHashCode();
        }

        return md5(
            $hashElements .
            __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
