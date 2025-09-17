<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as ActualWorksheet;

class StringTable extends WriterPart
{
    /**
     * Create worksheet stringtable.
     *
     * @param string[] $existingTable Existing table to eventually merge with
     *
     * @return string[] String table for worksheet
     */
    public function createStringTable(ActualWorksheet $worksheet, ?array $existingTable = null): array
    {
        // Create string lookup table
        /** @var string[] */
        $aStringTable = $existingTable ?? [];

        // Fill index array
        $aFlippedStringTable = $this->flipStringTable($aStringTable);

        // Loop through cells
        foreach ($worksheet->getCellCollection()->getCoordinates() as $coordinate) {
            /** @var Cell $cell */
            $cell = $worksheet->getCellCollection()->get($coordinate);
            /** @var null|int|RichText|string */
            $cellValue = $cell->getValue();
            if (
                !is_object($cellValue)
                && ($cellValue !== null)
                && $cellValue !== ''
                && ($cell->getDataType() == DataType::TYPE_STRING || $cell->getDataType() == DataType::TYPE_STRING2 || $cell->getDataType() == DataType::TYPE_NULL)
                && !isset($aFlippedStringTable[$cellValue])
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue] = true;
            } elseif (
                $cellValue instanceof RichText
                && !isset($aFlippedStringTable[$cellValue->getHashCode()])
            ) {
                $aStringTable[] = $cellValue;
                $aFlippedStringTable[$cellValue->getHashCode()] = true;
            }
        }
        /** @var string[] $aStringTable */

        return $aStringTable;
    }

    /**
     * Write string table to XML format.
     *
     * @param (RichText|string)[] $stringTable
     *
     * @return string XML Output
     */
    public function writeStringTable(array $stringTable): string
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
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('uniqueCount', (string) count($stringTable));

        // Loop through string table
        foreach ($stringTable as $textElement) {
            $objWriter->startElement('si');

            if (!($textElement instanceof RichText)) {
                $textToWrite = StringHelper::controlCharacterPHP2OOXML($textElement);
                $objWriter->startElement('t');
                if ($textToWrite !== trim($textToWrite)) {
                    $objWriter->writeAttribute('xml:space', 'preserve');
                }
                $objWriter->writeRawData($textToWrite);
                $objWriter->endElement();
            } else {
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
     * @param ?string $prefix Optional Namespace prefix
     */
    public function writeRichText(XMLWriter $objWriter, RichText $richText, ?string $prefix = null, ?Font $defaultFont = null): void
    {
        if ($prefix !== null) {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $richText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix . 'r');
            $font = ($element instanceof Run) ? $element->getFont() : $defaultFont;

            // rPr
            if ($font !== null) {
                // rPr
                $objWriter->startElement($prefix . 'rPr');

                // rFont
                if ($font->getName() !== null) {
                    $objWriter->startElement($prefix . 'rFont');
                    $objWriter->writeAttribute('val', $font->getName());
                    $objWriter->endElement();
                }

                // Bold
                $objWriter->startElement($prefix . 'b');
                $objWriter->writeAttribute('val', ($font->getBold() ? 'true' : 'false'));
                $objWriter->endElement();

                // Italic
                $objWriter->startElement($prefix . 'i');
                $objWriter->writeAttribute('val', ($font->getItalic() ? 'true' : 'false'));
                $objWriter->endElement();

                // Superscript / subscript
                if ($font->getSuperscript() || $font->getSubscript()) {
                    $objWriter->startElement($prefix . 'vertAlign');
                    if ($font->getSuperscript()) {
                        $objWriter->writeAttribute('val', 'superscript');
                    } elseif ($font->getSubscript()) {
                        $objWriter->writeAttribute('val', 'subscript');
                    }
                    $objWriter->endElement();
                }

                // Strikethrough
                $objWriter->startElement($prefix . 'strike');
                $objWriter->writeAttribute('val', ($font->getStrikethrough() ? 'true' : 'false'));
                $objWriter->endElement();

                // Color
                if ($font->getColor()->getARGB() !== null) {
                    $objWriter->startElement($prefix . 'color');
                    $objWriter->writeAttribute('rgb', $font->getColor()->getARGB());
                    $objWriter->endElement();
                }

                // Size
                if ($font->getSize() !== null) {
                    $objWriter->startElement($prefix . 'sz');
                    $objWriter->writeAttribute('val', (string) $font->getSize());
                    $objWriter->endElement();
                }

                // Underline
                if ($font->getUnderline() !== null) {
                    $objWriter->startElement($prefix . 'u');
                    $objWriter->writeAttribute('val', $font->getUnderline());
                    $objWriter->endElement();
                }

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
     * @param RichText|string $richText text string or Rich text
     * @param string $prefix Optional Namespace prefix
     */
    public function writeRichTextForCharts(XMLWriter $objWriter, $richText = null, string $prefix = ''): void
    {
        if (!($richText instanceof RichText)) {
            $textRun = $richText;
            $richText = new RichText();
            $run = $richText->createTextRun($textRun ?? '');
            $run->setFont(null);
        }

        if ($prefix !== '') {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $richText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix . 'r');
            if ($element->getFont() !== null) {
                // rPr
                $objWriter->startElement($prefix . 'rPr');
                $fontSize = $element->getFont()->getSize();
                if (is_numeric($fontSize)) {
                    $fontSize *= (($fontSize < 100) ? 100 : 1);
                    $objWriter->writeAttribute('sz', (string) $fontSize);
                }

                // Bold
                $objWriter->writeAttribute('b', ($element->getFont()->getBold() ? '1' : '0'));
                // Italic
                $objWriter->writeAttribute('i', ($element->getFont()->getItalic() ? '1' : '0'));
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
                if ($underlineType !== null) {
                    $objWriter->writeAttribute('u', $underlineType);
                }
                // Strikethrough
                $objWriter->writeAttribute('strike', ($element->getFont()->getStriketype() ?: 'noStrike'));
                // Superscript/subscript
                if ($element->getFont()->getBaseLine()) {
                    $objWriter->writeAttribute('baseline', (string) $element->getFont()->getBaseLine());
                }

                // Color
                $this->writeChartTextColor($objWriter, $element->getFont()->getChartColor(), $prefix);

                // Underscore Color
                $this->writeChartTextColor($objWriter, $element->getFont()->getUnderlineColor(), $prefix, 'uFill');

                // fontName
                if ($element->getFont()->getLatin()) {
                    $objWriter->startElement($prefix . 'latin');
                    $objWriter->writeAttribute('typeface', $element->getFont()->getLatin());
                    $objWriter->endElement();
                }
                if ($element->getFont()->getEastAsian()) {
                    $objWriter->startElement($prefix . 'ea');
                    $objWriter->writeAttribute('typeface', $element->getFont()->getEastAsian());
                    $objWriter->endElement();
                }
                if ($element->getFont()->getComplexScript()) {
                    $objWriter->startElement($prefix . 'cs');
                    $objWriter->writeAttribute('typeface', $element->getFont()->getComplexScript());
                    $objWriter->endElement();
                }

                $objWriter->endElement();
            }

            // t
            $objWriter->startElement($prefix . 't');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    private function writeChartTextColor(XMLWriter $objWriter, ?ChartColor $underlineColor, string $prefix, ?string $openTag = ''): void
    {
        if ($underlineColor !== null) {
            $type = $underlineColor->getType();
            $value = $underlineColor->getValue();
            if (!empty($type) && !empty($value)) {
                if ($openTag !== '') {
                    $objWriter->startElement($prefix . $openTag);
                }
                $objWriter->startElement($prefix . 'solidFill');
                $objWriter->startElement($prefix . $type);
                $objWriter->writeAttribute('val', $value);
                $alpha = $underlineColor->getAlpha();
                if (is_numeric($alpha)) {
                    $objWriter->startElement($prefix . 'alpha');
                    $objWriter->writeAttribute('val', ChartColor::alphaToXml((int) $alpha));
                    $objWriter->endElement();
                }
                $objWriter->endElement(); // srgbClr/schemeClr/prstClr
                $objWriter->endElement(); // solidFill
                if ($openTag !== '') {
                    $objWriter->endElement(); // uFill
                }
            }
        }
    }

    /**
     * Flip string table (for index searching).
     *
     * @param array<RichText|string> $stringTable Stringtable
     *
     * @return array<RichText|string>
     */
    public function flipStringTable(array $stringTable): array
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
