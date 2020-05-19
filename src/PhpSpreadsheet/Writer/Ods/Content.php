<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Comment;

/**
 * @author     Alexander Pervakov <frost-nzcr4@jagmort.com>
 */
class Content extends WriterPart
{
    const NUMBER_COLS_REPEATED_MAX = 1024;
    const NUMBER_ROWS_REPEATED_MAX = 1048576;
    const CELL_STYLE_PREFIX = 'ce';

    /**
     * Write content.xml to XML format.
     *
     * @return string XML Output
     */
    public function write()
    {
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Content
        $objWriter->startElement('office:document-content');
        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:style', 'urn:oasis:names:tc:opendocument:xmlns:style:1.0');
        $objWriter->writeAttribute('xmlns:text', 'urn:oasis:names:tc:opendocument:xmlns:text:1.0');
        $objWriter->writeAttribute('xmlns:table', 'urn:oasis:names:tc:opendocument:xmlns:table:1.0');
        $objWriter->writeAttribute('xmlns:draw', 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0');
        $objWriter->writeAttribute('xmlns:fo', 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $objWriter->writeAttribute('xmlns:number', 'urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0');
        $objWriter->writeAttribute('xmlns:presentation', 'urn:oasis:names:tc:opendocument:xmlns:presentation:1.0');
        $objWriter->writeAttribute('xmlns:svg', 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0');
        $objWriter->writeAttribute('xmlns:chart', 'urn:oasis:names:tc:opendocument:xmlns:chart:1.0');
        $objWriter->writeAttribute('xmlns:dr3d', 'urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0');
        $objWriter->writeAttribute('xmlns:math', 'http://www.w3.org/1998/Math/MathML');
        $objWriter->writeAttribute('xmlns:form', 'urn:oasis:names:tc:opendocument:xmlns:form:1.0');
        $objWriter->writeAttribute('xmlns:script', 'urn:oasis:names:tc:opendocument:xmlns:script:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('xmlns:ooow', 'http://openoffice.org/2004/writer');
        $objWriter->writeAttribute('xmlns:oooc', 'http://openoffice.org/2004/calc');
        $objWriter->writeAttribute('xmlns:dom', 'http://www.w3.org/2001/xml-events');
        $objWriter->writeAttribute('xmlns:xforms', 'http://www.w3.org/2002/xforms');
        $objWriter->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $objWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $objWriter->writeAttribute('xmlns:rpt', 'http://openoffice.org/2005/report');
        $objWriter->writeAttribute('xmlns:of', 'urn:oasis:names:tc:opendocument:xmlns:of:1.2');
        $objWriter->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        $objWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $objWriter->writeAttribute('xmlns:tableooo', 'http://openoffice.org/2009/table');
        $objWriter->writeAttribute('xmlns:field', 'urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0');
        $objWriter->writeAttribute('xmlns:formx', 'urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0');
        $objWriter->writeAttribute('xmlns:css3t', 'http://www.w3.org/TR/css3-text/');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->writeElement('office:scripts');
        $objWriter->writeElement('office:font-face-decls');

        // Styles XF
        $objWriter->startElement('office:automatic-styles');
        $this->writeXfStyles($objWriter, $this->getParentWriter()->getSpreadsheet());
        $objWriter->endElement();

        $objWriter->startElement('office:body');
        $objWriter->startElement('office:spreadsheet');
        $objWriter->writeElement('table:calculation-settings');

        $this->writeSheets($objWriter);

        $objWriter->writeElement('table:named-expressions');
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write sheets.
     */
    private function writeSheets(XMLWriter $objWriter): void
    {
        $spreadsheet = $this->getParentWriter()->getSpreadsheet(); /** @var Spreadsheet $spreadsheet */
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $objWriter->startElement('table:table');
            $objWriter->writeAttribute('table:name', $spreadsheet->getSheet($i)->getTitle());
            $objWriter->writeElement('office:forms');
            $objWriter->startElement('table:table-column');
            $objWriter->writeAttribute('table:number-columns-repeated', self::NUMBER_COLS_REPEATED_MAX);
            $objWriter->endElement();
            $this->writeRows($objWriter, $spreadsheet->getSheet($i));
            $objWriter->endElement();
        }
    }

    /**
     * Write rows of the specified sheet.
     */
    private function writeRows(XMLWriter $objWriter, Worksheet $sheet): void
    {
        $numberRowsRepeated = self::NUMBER_ROWS_REPEATED_MAX;
        $span_row = 0;
        $rows = $sheet->getRowIterator();
        while ($rows->valid()) {
            --$numberRowsRepeated;
            $row = $rows->current();
            if ($row->getCellIterator()->valid()) {
                if ($span_row) {
                    $objWriter->startElement('table:table-row');
                    if ($span_row > 1) {
                        $objWriter->writeAttribute('table:number-rows-repeated', $span_row);
                    }
                    $objWriter->startElement('table:table-cell');
                    $objWriter->writeAttribute('table:number-columns-repeated', self::NUMBER_COLS_REPEATED_MAX);
                    $objWriter->endElement();
                    $objWriter->endElement();
                    $span_row = 0;
                }
                $objWriter->startElement('table:table-row');
                $this->writeCells($objWriter, $row);
                $objWriter->endElement();
            } else {
                ++$span_row;
            }
            $rows->next();
        }
    }

    /**
     * Write cells of the specified row.
     */
    private function writeCells(XMLWriter $objWriter, Row $row): void
    {
        $numberColsRepeated = self::NUMBER_COLS_REPEATED_MAX;
        $prevColumn = -1;
        $cells = $row->getCellIterator();
        while ($cells->valid()) {
            /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
            $cell = $cells->current();
            $column = Coordinate::columnIndexFromString($cell->getColumn()) - 1;

            $this->writeCellSpan($objWriter, $column, $prevColumn);
            $objWriter->startElement('table:table-cell');
            $this->writeCellMerge($objWriter, $cell);

            // Style XF
            $style = $cell->getXfIndex();
            if ($style !== null) {
                $objWriter->writeAttribute('table:style-name', self::CELL_STYLE_PREFIX . $style);
            }

            switch ($cell->getDataType()) {
                case DataType::TYPE_BOOL:
                    $objWriter->writeAttribute('office:value-type', 'boolean');
                    $objWriter->writeAttribute('office:value', $cell->getValue());
                    $objWriter->writeElement('text:p', $cell->getValue());

                    break;
                case DataType::TYPE_ERROR:
                    throw new Exception('Writing of error not implemented yet.');

                    break;
                case DataType::TYPE_FORMULA:
                    $formulaValue = $cell->getValue();
                    if ($this->getParentWriter()->getPreCalculateFormulas()) {
                        try {
                            $formulaValue = $cell->getCalculatedValue();
                        } catch (Exception $e) {
                            // don't do anything
                        }
                    }
                    $objWriter->writeAttribute('table:formula', 'of:' . $cell->getValue());
                    if (is_numeric($formulaValue)) {
                        $objWriter->writeAttribute('office:value-type', 'float');
                    } else {
                        $objWriter->writeAttribute('office:value-type', 'string');
                    }
                    $objWriter->writeAttribute('office:value', $formulaValue);
                    $objWriter->writeElement('text:p', $formulaValue);

                    break;
                case DataType::TYPE_INLINE:
                    throw new Exception('Writing of inline not implemented yet.');

                    break;
                case DataType::TYPE_NUMERIC:
                    $objWriter->writeAttribute('office:value-type', 'float');
                    $objWriter->writeAttribute('office:value', $cell->getValue());
                    $objWriter->writeElement('text:p', $cell->getValue());

                    break;
                case DataType::TYPE_STRING:
                    $objWriter->writeAttribute('office:value-type', 'string');
                    $objWriter->writeElement('text:p', $cell->getValue());

                    break;
            }
            Comment::write($objWriter, $cell);
            $objWriter->endElement();
            $prevColumn = $column;
            $cells->next();
        }
        $numberColsRepeated = $numberColsRepeated - $prevColumn - 1;
        if ($numberColsRepeated > 0) {
            if ($numberColsRepeated > 1) {
                $objWriter->startElement('table:table-cell');
                $objWriter->writeAttribute('table:number-columns-repeated', $numberColsRepeated);
                $objWriter->endElement();
            } else {
                $objWriter->writeElement('table:table-cell');
            }
        }
    }

    /**
     * Write span.
     *
     * @param int $curColumn
     * @param int $prevColumn
     */
    private function writeCellSpan(XMLWriter $objWriter, $curColumn, $prevColumn): void
    {
        $diff = $curColumn - $prevColumn - 1;
        if (1 === $diff) {
            $objWriter->writeElement('table:table-cell');
        } elseif ($diff > 1) {
            $objWriter->startElement('table:table-cell');
            $objWriter->writeAttribute('table:number-columns-repeated', $diff);
            $objWriter->endElement();
        }
    }

    /**
     * Write XF cell styles.
     */
    private function writeXfStyles(XMLWriter $writer, Spreadsheet $spreadsheet): void
    {
        foreach ($spreadsheet->getCellXfCollection() as $style) {
            $writer->startElement('style:style');
            $writer->writeAttribute('style:name', self::CELL_STYLE_PREFIX . $style->getIndex());
            $writer->writeAttribute('style:family', 'table-cell');
            $writer->writeAttribute('style:parent-style-name', 'Default');

            // style:text-properties

            // Font
            $writer->startElement('style:text-properties');

            $font = $style->getFont();

            if ($font->getBold()) {
                $writer->writeAttribute('fo:font-weight', 'bold');
                $writer->writeAttribute('style:font-weight-complex', 'bold');
                $writer->writeAttribute('style:font-weight-asian', 'bold');
            }

            if ($font->getItalic()) {
                $writer->writeAttribute('fo:font-style', 'italic');
            }

            if ($color = $font->getColor()) {
                $writer->writeAttribute('fo:color', sprintf('#%s', $color->getRGB()));
            }

            if ($family = $font->getName()) {
                $writer->writeAttribute('fo:font-family', $family);
            }

            if ($size = $font->getSize()) {
                $writer->writeAttribute('fo:font-size', sprintf('%.1Fpt', $size));
            }

            if ($font->getUnderline() && $font->getUnderline() != Font::UNDERLINE_NONE) {
                $writer->writeAttribute('style:text-underline-style', 'solid');
                $writer->writeAttribute('style:text-underline-width', 'auto');
                $writer->writeAttribute('style:text-underline-color', 'font-color');

                switch ($font->getUnderline()) {
                    case Font::UNDERLINE_DOUBLE:
                        $writer->writeAttribute('style:text-underline-type', 'double');

                        break;
                    case Font::UNDERLINE_SINGLE:
                        $writer->writeAttribute('style:text-underline-type', 'single');

                        break;
                }
            }

            $writer->endElement(); // Close style:text-properties

            // style:table-cell-properties

            $writer->startElement('style:table-cell-properties');
            $writer->writeAttribute('style:rotation-align', 'none');

            // Fill
            if ($fill = $style->getFill()) {
                switch ($fill->getFillType()) {
                    case Fill::FILL_SOLID:
                        $writer->writeAttribute('fo:background-color', sprintf(
                            '#%s',
                            strtolower($fill->getStartColor()->getRGB())
                        ));

                        break;
                    case Fill::FILL_GRADIENT_LINEAR:
                    case Fill::FILL_GRADIENT_PATH:
                        /// TODO :: To be implemented
                        break;
                    case Fill::FILL_NONE:
                    default:
                }
            }

            $writer->endElement(); // Close style:table-cell-properties

            // End

            $writer->endElement(); // Close style:style
        }
    }

    /**
     * Write attributes for merged cell.
     */
    private function writeCellMerge(XMLWriter $objWriter, Cell $cell): void
    {
        if (!$cell->isMergeRangeValueCell()) {
            return;
        }

        $mergeRange = Coordinate::splitRange($cell->getMergeRange());
        [$startCell, $endCell] = $mergeRange[0];
        $start = Coordinate::coordinateFromString($startCell);
        $end = Coordinate::coordinateFromString($endCell);
        $columnSpan = Coordinate::columnIndexFromString($end[0]) - Coordinate::columnIndexFromString($start[0]) + 1;
        $rowSpan = $end[1] - $start[1] + 1;

        $objWriter->writeAttribute('table:number-columns-spanned', $columnSpan);
        $objWriter->writeAttribute('table:number-rows-spanned', $rowSpan);
    }
}
