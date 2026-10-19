<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table as WorksheetTable;

class Table extends WriterPart
{
    /**
     * Write Table to XML format.
     *
     * @param int $tableRef Table ID
     *
     * @return string XML Output
     */
    public function writeTable(WorksheetTable $table, int $tableRef): string
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

        // Table
        $name = 'Table' . $tableRef;
        $range = $this->rangeWithRoomForData($table);

        $objWriter->startElement('table');
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);
        $objWriter->writeAttribute('id', (string) $tableRef);
        $objWriter->writeAttribute('name', $name);
        $objWriter->writeAttribute('displayName', $table->getName() ?: $name);
        $objWriter->writeAttribute('ref', $range);
        $objWriter->writeAttribute('headerRowCount', $table->getShowHeaderRow() ? '1' : '0');
        $objWriter->writeAttribute('totalsRowCount', $table->getShowTotalsRow() ? '1' : '0');

        // Table Boundaries
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($table->getRange());

        // Table Auto Filter
        if ($table->getShowHeaderRow() && $table->getAllowFilter() === true) {
            $objWriter->startElement('autoFilter');
            $objWriter->writeAttribute('ref', $range);
            foreach (range($rangeStart[0], $rangeEnd[0]) as $offset => $columnIndex) {
                $column = $table->getColumnByOffset($offset);

                if (!$column->getShowFilterButton()) {
                    $objWriter->startElement('filterColumn');
                    $objWriter->writeAttribute('colId', (string) $offset);
                    $objWriter->writeAttribute('hiddenButton', '1');
                    $objWriter->endElement();
                } else {
                    $column = $table->getAutoFilter()->getColumnByOffset($offset);
                    AutoFilter::writeAutoFilterColumn($objWriter, $column, $offset);
                }
            }
            $objWriter->endElement(); // autoFilter
        }

        // Table Columns
        $objWriter->startElement('tableColumns');
        $objWriter->writeAttribute('count', (string) ($rangeEnd[0] - $rangeStart[0] + 1));
        foreach (range($rangeStart[0], $rangeEnd[0]) as $offset => $columnIndex) {
            $worksheet = $table->getWorksheet();
            if (!$worksheet) {
                continue;
            }

            $column = $table->getColumnByOffset($offset);
            $cell = $worksheet->getCell([$columnIndex, $rangeStart[1]]);

            $objWriter->startElement('tableColumn');
            $objWriter->writeAttribute('id', (string) ($offset + 1));
            $objWriter->writeAttribute('name', $table->getShowHeaderRow() ? $cell->getValueString() : ('Column' . ($offset + 1)));

            if ($table->getShowTotalsRow()) {
                if ($column->getTotalsRowLabel()) {
                    $objWriter->writeAttribute('totalsRowLabel', $column->getTotalsRowLabel());
                }
                if ($column->getTotalsRowFunction()) {
                    $objWriter->writeAttribute('totalsRowFunction', $column->getTotalsRowFunction());
                }
            }
            if ($column->getColumnFormula()) {
                $objWriter->writeElement('calculatedColumnFormula', $column->getColumnFormula());
            }

            $objWriter->endElement();
        }
        $objWriter->endElement();

        // Table Styles
        $objWriter->startElement('tableStyleInfo');
        $objWriter->writeAttribute('name', $table->getStyle()->getTheme());
        $objWriter->writeAttribute('showFirstColumn', $table->getStyle()->getShowFirstColumn() ? '1' : '0');
        $objWriter->writeAttribute('showLastColumn', $table->getStyle()->getShowLastColumn() ? '1' : '0');
        $objWriter->writeAttribute('showRowStripes', $table->getStyle()->getShowRowStripes() ? '1' : '0');
        $objWriter->writeAttribute('showColumnStripes', $table->getStyle()->getShowColumnStripes() ? '1' : '0');
        $objWriter->endElement();

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * The range to write, given that a table showing a header row needs a row of data under it.
     *
     * Excel reports a workbook whose table covers its header row alone as unreadable, and repairs
     * it by dropping the table — the markup is lost without a word. Excel itself never writes such
     * a table: asked to make one over a single row of headings, it writes the table over the row
     * below as well, and leaves that row empty (its `sheetData` holds no cell for it). This does
     * the same, so what is written is what Excel would have written.
     *
     * The row below is taken only when it holds nothing: a table silently swallowing a row that
     * belongs to something else would change what the sheet says, so that case is refused instead.
     *
     * @throws PhpSpreadsheetException
     */
    private function rangeWithRoomForData(WorksheetTable $table): string
    {
        $range = $table->getRange();

        if (!$table->getShowHeaderRow()) {
            return $range;
        }

        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($range);

        if ($rangeEnd[1] > $rangeStart[1]) {
            return $range;
        }

        $worksheet = $table->getWorksheet();
        $rowBelow = $rangeEnd[1] + 1;

        if ($worksheet !== null && $worksheet->getHighestDataRow() >= $rowBelow) {
            throw new PhpSpreadsheetException(
                'Table ' . $table->getName() . ' shows a header row over ' . $range
                . ', which leaves no row for its data, and row ' . $rowBelow
                . ' below it is not empty; a table with a header row needs at least 2 rows'
            );
        }

        return Coordinate::stringFromColumnIndex($rangeStart[0]) . $rangeStart[1]
            . ':' . Coordinate::stringFromColumnIndex($rangeEnd[0]) . $rowBelow;
    }
}
