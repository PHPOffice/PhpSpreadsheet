<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StringTable extends WriterPart
{
    /**
     * Create worksheet stringtable.
     *
     * @param Worksheet $worksheet Worksheet
     * @param string[] $existingTable Existing table to eventually merge with
     *
     * @return string[] String table for worksheet
     */
    public function createStringTable(Worksheet $worksheet, $existingTable = null)
    {
        // Create string lookup table
        $aStringTable = [];
        $cellCollection = null;
        $aFlippedStringTable = null; // For faster lookup

        // Is an existing table given?
        if (($existingTable !== null) && is_array($existingTable)) {
            $aStringTable = $existingTable;
        }

        // Fill index array
        $aFlippedStringTable = $this->flipStringTable($aStringTable);

        // Loop through cells
        foreach ($worksheet->getCoordinates() as $coordinate) {
            $cell = $worksheet->getCell($coordinate);
            $cellValue = $cell->getValue();
            if (
                !is_object($cellValue) &&
                ($cellValue !== null) &&
                $cellValue !== '' &&
                ($cell->getDataType() == DataType::TYPE_STRING || $cell->getDataType() == DataType::TYPE_STRING2 || $cell->getDataType() == DataType::TYPE_NULL) &&
                !isset($aFlippedStringTable[$cellValue])
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue] = true;
            } elseif (
                $cellValue instanceof RichText &&
                ($cellValue !== null) &&
                !isset($aFlippedStringTable[$cellValue->getHashCode()])
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue->getHashCode()] = true;
            }
        }

        return $aStringTable;
    }

    /**
     * Write string table to XML format.
     *
     * @param string[] $pStringTable
     *
     * @return string XML Output
     */
    public function writeStringTable(array $pStringTable)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // String table
        $objWriter->startElement('sst');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $objWriter->writeAttribute('uniqueCount', count($pStringTable));

        // Loop through string table
        foreach ($pStringTable as $textElement) {
            $objWriter->startElement('si');

            if (!$textElement instanceof RichText) {
                $textToWrite = StringHelper::controlCharacterPHP2OOXML($textElement);
                $objWriter->startElement('t');
                if ($textToWrite !== trim($textToWrite)) {
                    $objWriter->writeAttribute('xml:space', 'preserve');
                }
                $objWriter->writeRawData($textToWrite);
                $objWriter->endElement();
            } elseif ($textElement instanceof RichText) {
                $this->writeRichText($objWriter, $textElement);
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write Rich Text.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param RichText $pRichText Rich text
     * @param string $prefix Optional Namespace prefix
     */
    public function writeRichText(XMLWriter $objWriter, RichText $pRichText, $prefix = null): void
    {
        if ($prefix !== null) {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix . 'r');

            // rPr
            if ($element instanceof Run) {
                // rPr
                $objWriter->startElement($prefix . 'rPr');

                // rFont
                $objWriter->startElement($prefix . 'rFont');
                $objWriter->writeAttribute('val', $element->getFont()->getName());
                $objWriter->endElement();

                // Bold
                $objWriter->startElement($prefix . 'b');
                $objWriter->writeAttribute('val', ($element->getFont()->getBold() ? 'true' : 'false'));
                $objWriter->endElement();

                // Italic
                $objWriter->startElement($prefix . 'i');
                $objWriter->writeAttribute('val', ($element->getFont()->getItalic() ? 'true' : 'false'));
                $objWriter->endElement();

                // Superscript / subscript
                if ($element->getFont()->getSuperscript() || $element->getFont()->getSubscript()) {
                    $objWriter->startElement($prefix . 'vertAlign');
                    if ($element->getFont()->getSuperscript()) {
                        $objWriter->writeAttribute('val', 'superscript');
                    } elseif ($element->getFont()->getSubscript()) {
                        $objWriter->writeAttribute('val', 'subscript');
                    }
                    $objWriter->endElement();
                }

                // Strikethrough
                $objWriter->startElement($prefix . 'strike');
                $objWriter->writeAttribute('val', ($element->getFont()->getStrikethrough() ? 'true' : 'false'));
                $objWriter->endElement();

                // Color
                $objWriter->startElement($prefix . 'color');
                $objWriter->writeAttribute('rgb', $element->getFont()->getColor()->getARGB());
                $objWriter->endElement();

                // Size
                $objWriter->startElement($prefix . 'sz');
                $objWriter->writeAttribute('val', $element->getFont()->getSize());
                $objWriter->endElement();

                // Underline
                $objWriter->startElement($prefix . 'u');
                $objWriter->writeAttribute('val', $element->getFont()->getUnderline());
                $objWriter->endElement();

                $objWriter->endElement();
            }

            // t
            $objWriter->startElement($prefix . 't');
            $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write Rich Text.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param RichText|string $pRichText text string or Rich text
     * @param string $prefix Optional Namespace prefix
     */
    public function writeRichTextForCharts(XMLWriter $objWriter, $pRichText = null, $prefix = null): void
    {
        if (!$pRichText instanceof RichText) {
            $textRun = $pRichText;
            $pRichText = new RichText();
            $pRichText->createTextRun($textRun);
        }

        if ($prefix !== null) {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix . 'r');

            // rPr
            $objWriter->startElement($prefix . 'rPr');

            // Bold
            $objWriter->writeAttribute('b', ($element->getFont()->getBold() ? 1 : 0));
            // Italic
            $objWriter->writeAttribute('i', ($element->getFont()->getItalic() ? 1 : 0));
            // Underline
            $underlineType = $element->getFont()->getUnderline();
            switch ($underlineType) {
                case 'single':
                    $underlineType = 'sng';

                    break;
                case 'double':
                    $underlineType = 'dbl';

                    break;
            }
            $objWriter->writeAttribute('u', $underlineType);
            // Strikethrough
            $objWriter->writeAttribute('strike', ($element->getFont()->getStrikethrough() ? 'sngStrike' : 'noStrike'));

            // rFont
            $objWriter->startElement($prefix . 'latin');
            $objWriter->writeAttribute('typeface', $element->getFont()->getName());
            $objWriter->endElement();

            $objWriter->endElement();

            // t
            $objWriter->startElement($prefix . 't');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Flip string table (for index searching).
     *
     * @param array $stringTable Stringtable
     *
     * @return array
     */
    public function flipStringTable(array $stringTable)
    {
        // Return value
        $returnValue = [];

        // Loop through stringtable and add flipped items to $returnValue
        foreach ($stringTable as $key => $value) {
            if (!$value instanceof RichText) {
                $returnValue[$value] = $key;
            } elseif ($value instanceof RichText) {
                $returnValue[$value->getHashCode()] = $key;
            }
        }

        return $returnValue;
    }
}
