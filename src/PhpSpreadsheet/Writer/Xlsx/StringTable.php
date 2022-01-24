<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class StringTable extends WriterPart
{
    /**
     * @var XMLWriter
     */
    private $objWriter;

    /**
     * @var int
     */
    private $uniqueCount;

    /**
     * In appended mode,use a temporary path to store
     * @var string
     */
    private $tempFilePath;

    /**
     * In appended mode,record file where to be stored
     * @var string
     */
    private $userPath;

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
     * @param string[] $stringTable
     *
     * @return string XML Output
     */
    public function writeStringTable(array $stringTable)
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
        $objWriter->writeAttribute('uniqueCount', count($stringTable));

        // Loop through string table
        foreach ($stringTable as $textElement) {
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
     * @return StringTable
     * @throws WriterException
     */
    public function createDiskCacheWriter()
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $this->userPath = $this->getParentWriter()->getFileStorePath();
            $this->tempFilePath = @tempnam(dirname($this->userPath), 'xml') ?: '';
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->tempFilePath, true);
            $objWriter->needUnlink = false;
        } else {
            throw new WriterException("ParentWriter's useDiskCaching is false");
        }
        $this->objWriter = $objWriter;

        return $this;
    }

    /**
     * In order to write corrected content, flush data when each called
     * @param array $stringTable
     * @return $this
     */
    public function writeStringTableWithAppendedMode(array $stringTable)
    {
        //markdown the final uniqueCount
        $this->uniqueCount += count($stringTable);
        $objWriter = $this->objWriter;

        // Loop through string table
        foreach ($stringTable as $textElement) {
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

        $objWriter->flush();

        return $this;
    }

    /**
     * Create worksheet stringtable.
     *
     * @param Worksheet $pSheet Worksheet
     * @param string[] $pExistingTable Existing table to eventually merge with
     * @param array|null $existingTable
     *
     * @return string[] String table for worksheet
     */
    public function createStringTableWithStartIndex(Worksheet $pSheet, $existingTable = null)
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
        $index = $this->uniqueCount;

        // Loop through cells
        foreach ($pSheet->getCoordinates() as $coordinate) {
            $cell = $pSheet->getCell($coordinate);
            $cellValue = $cell->getValue();
            if (
                !is_object($cellValue) &&
                ($cellValue !== null) &&
                $cellValue !== '' &&
                !isset($aFlippedStringTable[$cellValue]) &&
                ($cell->getDataType() == DataType::TYPE_STRING || $cell->getDataType() == DataType::TYPE_STRING2 || $cell->getDataType() == DataType::TYPE_NULL)
            ) {
                $aStringTable[$index] = $cellValue;
                ++$index;
                $aFlippedStringTable[$cellValue] = true;
            } elseif (
                $cellValue instanceof RichText &&
                ($cellValue !== null) &&
                !isset($aFlippedStringTable[$cellValue->getHashCode()])
            ) {
                $aStringTable[$index] = $cellValue;
                $aFlippedStringTable[$cellValue->getHashCode()] = true;
                ++$index;
            }
        }

        return $aStringTable;
    }

    /**
     * Write xml header and match right uniqueCount
     * @throws WriterException
     */
    public function writeStringTableEnd(): void
    {
        $fp1 = fopen($this->tempFilePath, 'r');
        $fp = fopen($this->userPath, 'a+');
        if (!is_resource($fp) || !is_resource($fp1)) {
            throw new WriterException('unable to open file:' . $this->tempFilePath . ' or ' . $this->userPath);
        }

        $str = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" uniqueCount="' . $this->uniqueCount . '">';
        fwrite($fp, $str);
        while (!feof($fp1)) {
            fwrite($fp, fread($fp1, 65536));
        }
        fwrite($fp, '</sst>');
        fclose($fp1);
        fclose($fp);
        unlink($this->tempFilePath);
    }

    /**
     * Write Rich Text.
     *
     * @param string $prefix Optional Namespace prefix
     */
    public function writeRichText(XMLWriter $objWriter, RichText $richText, $prefix = null): void
    {
        if ($prefix !== null) {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $richText->getRichTextElements();
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
     * @param RichText|string $richText text string or Rich text
     * @param string $prefix Optional Namespace prefix
     */
    public function writeRichTextForCharts(XMLWriter $objWriter, $richText = null, $prefix = null): void
    {
        if (!$richText instanceof RichText) {
            $textRun = $richText;
            $richText = new RichText();
            $richText->createTextRun($textRun);
        }

        if ($prefix !== null) {
            $prefix .= ':';
        }

        // Loop through rich text elements
        $elements = $richText->getRichTextElements();
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
