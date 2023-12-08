<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Title
{
    public const TITLE_CELL_REFERENCE
        = '/^(.*)!' // beginning of string, everything up to ! is match[1]
        . '[$]([A-Z]{1,3})' // absolute column string match[2]
        . '[$](\d{1,7})$/i'; // absolute row string match[3]

    /**
     * Title Caption.
     *
     * @var array|RichText|string
     */
    private $caption = '';

    /**
     * Allow overlay of other elements?
     *
     * @var bool
     */
    private $overlay = true;

    /**
     * Title Layout.
     */
    private ?Layout $layout;

    private string $cellReference = '';

    private ?Font $font = null;

    /**
     * Create a new Title.
     *
     * @param array|RichText|string $caption
     * @param bool $overlay
     */
    public function __construct($caption = '', ?Layout $layout = null, $overlay = false)
    {
        $this->caption = $caption;
        $this->layout = $layout;
        $this->setOverlay($overlay);
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

    public function getCaptionText(?Spreadsheet $spreadsheet = null): string
    {
        if ($spreadsheet !== null) {
            $caption = $this->getCalculatedTitle($spreadsheet);
            if ($caption !== null) {
                return $caption;
            }
        }
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
    public function setCaption($caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get allow overlay of other elements?
     *
     * @return bool
     */
    public function getOverlay()
    {
        return $this->overlay;
    }

    /**
     * Set allow overlay of other elements?
     *
     * @param bool $overlay
     */
    public function setOverlay($overlay): static
    {
        $this->overlay = $overlay;

        return $this;
    }

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    public function setCellReference(string $cellReference): self
    {
        $this->cellReference = $cellReference;

        return $this;
    }

    public function getCellReference(): string
    {
        return $this->cellReference;
    }

    public function getCalculatedTitle(?Spreadsheet $spreadsheet): ?string
    {
        preg_match(self::TITLE_CELL_REFERENCE, $this->cellReference, $matches);
        if (count($matches) === 0 || $spreadsheet === null) {
            return null;
        }
        $sheetName = preg_replace("/^'(.*)'$/", '$1', $matches[1]) ?? '';

        return $spreadsheet->getSheetByName($sheetName)?->getCell($matches[2] . $matches[3])?->getFormattedValue();
    }

    public function getFont(): ?Font
    {
        return $this->font;
    }

    public function setFont(?Font $font): self
    {
        $this->font = $font;

        return $this;
    }
}
