<?php

namespace PhpOffice\PhpSpreadsheet;

class RichText implements IComparable
{
    /**
     * Rich text elements.
     *
     * @var RichText\ITextElement[]
     */
    private $richTextElements;

    /**
     * Create a new RichText instance.
     *
     * @param Cell $pCell
     *
     * @throws Exception
     */
    public function __construct(Cell $pCell = null)
    {
        // Initialise variables
        $this->richTextElements = [];

        // Rich-Text string attached to cell?
        if ($pCell !== null) {
            // Add cell text and style
            if ($pCell->getValue() != '') {
                $objRun = new RichText\Run($pCell->getValue());
                $objRun->setFont(clone $pCell->getWorksheet()->getStyle($pCell->getCoordinate())->getFont());
                $this->addText($objRun);
            }

            // Set parent value
            $pCell->setValueExplicit($this, Cell\DataType::TYPE_STRING);
        }
    }

    /**
     * Add text.
     *
     * @param RichText\ITextElement $pText Rich text element
     *
     * @throws Exception
     *
     * @return RichText
     */
    public function addText(RichText\ITextElement $pText)
    {
        $this->richTextElements[] = $pText;

        return $this;
    }

    /**
     * Create text.
     *
     * @param string $pText Text
     *
     * @throws Exception
     *
     * @return RichText\TextElement
     */
    public function createText($pText)
    {
        $objText = new RichText\TextElement($pText);
        $this->addText($objText);

        return $objText;
    }

    /**
     * Create text run.
     *
     * @param string $pText Text
     *
     * @throws Exception
     *
     * @return RichText\Run
     */
    public function createTextRun($pText)
    {
        $objText = new RichText\Run($pText);
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

        // Loop through all RichText\ITextElements
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
     * @return RichText\ITextElement[]
     */
    public function getRichTextElements()
    {
        return $this->richTextElements;
    }

    /**
     * Set Rich Text elements.
     *
     * @param RichText\ITextElement[] $textElements Array of elements
     *
     * @throws Exception
     *
     * @return RichText
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
