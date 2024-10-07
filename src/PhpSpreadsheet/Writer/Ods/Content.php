<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Comment;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Style;

/**
 * @author     Alexander Pervakov <frost-nzcr4@jagmort.com>
 */
class Content extends WriterPart
{
    const NUMBER_COLS_REPEATED_MAX = 1024;
    const NUMBER_ROWS_REPEATED_MAX = 1048576;

    private Formula $formulaConvertor;

    /**
     * Set parent Ods writer.
     */
    public function __construct(Ods $writer)
    {
        parent::__construct($writer);

        $this->formulaConvertor = new Formula($this->getParentWriter()->getSpreadsheet()->getDefinedNames());
    }

    /**
     * Write content.xml to XML format.
     *
     * @return string XML Output
     */
    public function write(): string
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

        (new AutoFilters($objWriter, $this->getParentWriter()->getSpreadsheet()))->write();
        // Defined names (ranges and formulae)
        (new NamedExpressions($objWriter, $this->getParentWriter()->getSpreadsheet(), $this->formulaConvertor))->write();

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
        $spreadsheet = $this->getParentWriter()->getSpreadsheet();
        $sheetCount = $spreadsheet->getSheetCount();
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; ++$sheetIndex) {
            $spreadsheet->getSheet($sheetIndex)->calculateArrays($this->getParentWriter()->getPreCalculateFormulas());
            $objWriter->startElement('table:table');
            $objWriter->writeAttribute('table:name', $spreadsheet->getSheet($sheetIndex)->getTitle());
            $objWriter->writeAttribute('table:style-name', Style::TABLE_STYLE_PREFIX . (string) ($sheetIndex + 1));
            $objWriter->writeElement('office:forms');
            $lastColumn = 0;
            foreach ($spreadsheet->getSheet($sheetIndex)->getColumnDimensions() as $columnDimension) {
                $thisColumn = $columnDimension->getColumnNumeric();
                $emptyColumns = $thisColumn - $lastColumn - 1;
                if ($emptyColumns > 0) {
                    $objWriter->startElement('table:table-column');
                    $objWriter->writeAttribute('table:number-columns-repeated', (string) $emptyColumns);
                    $objWriter->endElement();
                }
                $lastColumn = $thisColumn;
                $objWriter->startElement('table:table-column');
                $objWriter->writeAttribute(
                    'table:style-name',
                    sprintf('%s_%d_%d', Style::COLUMN_STYLE_PREFIX, $sheetIndex, $columnDimension->getColumnNumeric())
                );
                $objWriter->writeAttribute('table:default-cell-style-name', 'ce0');
//                $objWriter->writeAttribute('table:number-columns-repeated', self::NUMBER_COLS_REPEATED_MAX);
                $objWriter->endElement();
            }
            $this->writeRows($objWriter, $spreadsheet->getSheet($sheetIndex), $sheetIndex);
            $objWriter->endElement();
        }
    }

    /**
     * Write rows of the specified sheet.
     */
    private function writeRows(XMLWriter $objWriter, Worksheet $sheet, int $sheetIndex): void
    {
        $numberRowsRepeated = self::NUMBER_ROWS_REPEATED_MAX;
        $span_row = 0;
        $rows = $sheet->getRowIterator();
        foreach ($rows as $row) {
            $cellIterator = $row->getCellIterator();
            --$numberRowsRepeated;
            if ($cellIterator->valid()) {
                $objWriter->startElement('table:table-row');
                if ($span_row) {
                    if ($span_row > 1) {
                        $objWriter->writeAttribute('table:number-rows-repeated', (string) $span_row);
                    }
                    $objWriter->startElement('table:table-cell');
                    $objWriter->writeAttribute('table:number-columns-repeated', (string) self::NUMBER_COLS_REPEATED_MAX);
                    $objWriter->endElement();
                    $span_row = 0;
                } else {
                    if ($sheet->rowDimensionExists($row->getRowIndex()) && $sheet->getRowDimension($row->getRowIndex())->getRowHeight() > 0) {
                        $objWriter->writeAttribute(
                            'table:style-name',
                            sprintf('%s_%d_%d', Style::ROW_STYLE_PREFIX, $sheetIndex, $row->getRowIndex())
                        );
                    }
                    $this->writeCells($objWriter, $cellIterator);
                }
                $objWriter->endElement();
            } else {
                ++$span_row;
            }
        }
    }

    /**
     * Write cells of the specified row.
     */
    private function writeCells(XMLWriter $objWriter, RowCellIterator $cells): void
    {
        $numberColsRepeated = self::NUMBER_COLS_REPEATED_MAX;
        $prevColumn = -1;
        foreach ($cells as $cell) {
            /** @var Cell $cell */
            $column = Coordinate::columnIndexFromString($cell->getColumn()) - 1;
            $attributes = $cell->getFormulaAttributes() ?? [];

            $this->writeCellSpan($objWriter, $column, $prevColumn);
            $objWriter->startElement('table:table-cell');
            $this->writeCellMerge($objWriter, $cell);

            // Style XF
            $style = $cell->getXfIndex();
            if ($style !== null) {
                $objWriter->writeAttribute('table:style-name', Style::CELL_STYLE_PREFIX . $style);
            }

            switch ($cell->getDataType()) {
                case DataType::TYPE_BOOL:
                    $objWriter->writeAttribute('office:value-type', 'boolean');
                    $objWriter->writeAttribute('office:boolean-value', $cell->getValue() ? 'true' : 'false');
                    $objWriter->writeElement('text:p', Calculation::getInstance()->getLocaleBoolean($cell->getValue() ? 'TRUE' : 'FALSE'));

                    break;
                case DataType::TYPE_ERROR:
                    $objWriter->writeAttribute('table:formula', 'of:=#NULL!');
                    $objWriter->writeAttribute('office:value-type', 'string');
                    $objWriter->writeAttribute('office:string-value', '');
                    $objWriter->writeElement('text:p', '#NULL!');

                    break;
                case DataType::TYPE_FORMULA:
                    $formulaValue = $cell->getValueString();
                    if ($this->getParentWriter()->getPreCalculateFormulas()) {
                        try {
                            $formulaValue = $cell->getCalculatedValueString();
                        } catch (CalculationException $e) {
                            // don't do anything
                        }
                    }
                    if (isset($attributes['ref'])) {
                        if (preg_match('/^([A-Z]{1,3})([0-9]{1,7})(:([A-Z]{1,3})([0-9]{1,7}))?$/', (string) $attributes['ref'], $matches) == 1) {
                            $matrixRowSpan = 1;
                            $matrixColSpan = 1;
                            if (isset($matches[3])) {
                                $minRow = (int) $matches[2];
                                // https://github.com/phpstan/phpstan/issues/11602
                                $maxRow = (int) $matches[5]; // @phpstan-ignore-line
                                $matrixRowSpan = $maxRow - $minRow + 1;
                                $minCol = Coordinate::columnIndexFromString($matches[1]);
                                $maxCol = Coordinate::columnIndexFromString($matches[4]); // @phpstan-ignore-line
                                $matrixColSpan = $maxCol - $minCol + 1;
                            }
                            $objWriter->writeAttribute('table:number-matrix-columns-spanned', "$matrixColSpan");
                            $objWriter->writeAttribute('table:number-matrix-rows-spanned', "$matrixRowSpan");
                        }
                    }
                    $objWriter->writeAttribute('table:formula', $this->formulaConvertor->convertFormula($cell->getValueString()));
                    if (is_numeric($formulaValue)) {
                        $objWriter->writeAttribute('office:value-type', 'float');
                    } else {
                        $objWriter->writeAttribute('office:value-type', 'string');
                    }
                    $objWriter->writeAttribute('office:value', $formulaValue);
                    $objWriter->writeElement('text:p', $formulaValue);

                    break;
                case DataType::TYPE_NUMERIC:
                    $objWriter->writeAttribute('office:value-type', 'float');
                    $objWriter->writeAttribute('office:value', $cell->getValueString());
                    $objWriter->writeElement('text:p', $cell->getValueString());

                    break;
                case DataType::TYPE_INLINE:
                    // break intentionally omitted
                case DataType::TYPE_STRING:
                    $objWriter->writeAttribute('office:value-type', 'string');
                    $url = $cell->getHyperlink()->getUrl();
                    if (empty($url)) {
                        $objWriter->writeElement('text:p', $cell->getValueString());
                    } else {
                        $objWriter->startElement('text:p');
                        $objWriter->startElement('text:a');
                        $sheets = 'sheet://';
                        $lensheets = strlen($sheets);
                        if (substr($url, 0, $lensheets) === $sheets) {
                            $url = '#' . substr($url, $lensheets);
                        }
                        $objWriter->writeAttribute('xlink:href', $url);
                        $objWriter->writeAttribute('xlink:type', 'simple');
                        $objWriter->text($cell->getValueString());
                        $objWriter->endElement(); // text:a
                        $objWriter->endElement(); // text:p
                    }

                    break;
            }
            Comment::write($objWriter, $cell);
            $objWriter->endElement();
            $prevColumn = $column;
        }

        $numberColsRepeated = $numberColsRepeated - $prevColumn - 1;
        if ($numberColsRepeated > 0) {
            if ($numberColsRepeated > 1) {
                $objWriter->startElement('table:table-cell');
                $objWriter->writeAttribute('table:number-columns-repeated', (string) $numberColsRepeated);
                $objWriter->endElement();
            } else {
                $objWriter->writeElement('table:table-cell');
            }
        }
    }

    /**
     * Write span.
     */
    private function writeCellSpan(XMLWriter $objWriter, int $curColumn, int $prevColumn): void
    {
        $diff = $curColumn - $prevColumn - 1;
        if (1 === $diff) {
            $objWriter->writeElement('table:table-cell');
        } elseif ($diff > 1) {
            $objWriter->startElement('table:table-cell');
            $objWriter->writeAttribute('table:number-columns-repeated', (string) $diff);
            $objWriter->endElement();
        }
    }

    /**
     * Write XF cell styles.
     */
    private function writeXfStyles(XMLWriter $writer, Spreadsheet $spreadsheet): void
    {
        $styleWriter = new Style($writer);

        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $worksheet = $spreadsheet->getSheet($i);
            $styleWriter->writeTableStyle($worksheet, $i + 1);

            $worksheet->calculateColumnWidths();
            foreach ($worksheet->getColumnDimensions() as $columnDimension) {
                if ($columnDimension->getWidth() !== -1.0) {
                    $styleWriter->writeColumnStyles($columnDimension, $i);
                }
            }
        }
        for ($i = 0; $i < $sheetCount; ++$i) {
            $worksheet = $spreadsheet->getSheet($i);
            foreach ($worksheet->getRowDimensions() as $rowDimension) {
                if ($rowDimension->getRowHeight() > 0.0) {
                    $styleWriter->writeRowStyles($rowDimension, $i);
                }
            }
        }

        foreach ($spreadsheet->getCellXfCollection() as $style) {
            $styleWriter->write($style);
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

        $mergeRange = Coordinate::splitRange((string) $cell->getMergeRange());
        [$startCell, $endCell] = $mergeRange[0];
        $start = Coordinate::coordinateFromString($startCell);
        $end = Coordinate::coordinateFromString($endCell);
        $columnSpan = Coordinate::columnIndexFromString($end[0]) - Coordinate::columnIndexFromString($start[0]) + 1;
        $rowSpan = ((int) $end[1]) - ((int) $start[1]) + 1;

        $objWriter->writeAttribute('table:number-columns-spanned', (string) $columnSpan);
        $objWriter->writeAttribute('table:number-rows-spanned', (string) $rowSpan);
    }
}
