<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
    public function writeTable(WorksheetTable $table, $tableRef): string
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
        $range = $table->getRange();

        $objWriter->startElement('table');
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $objWriter->writeAttribute('id', (string) $tableRef);
        $objWriter->writeAttribute('name', $name);
        $objWriter->writeAttribute('displayName', $table->getName() ?: $name);
        $objWriter->writeAttribute('ref', $range);
        $objWriter->writeAttribute('headerRowCount', $table->getShowHeaderRow() ? '1' : '0');
        $objWriter->writeAttribute('totalsRowCount', $table->getShowTotalsRow() ? '1' : '0');

        // Table Boundaries
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($table->getRange());

        // Table Auto Filter
        if ($table->getShowHeaderRow()) {
            $objWriter->startElement('autoFilter');
            $objWriter->writeAttribute('ref', $range);
            foreach (range($rangeStart[0], $rangeEnd[0]) as $offset => $columnIndex) {
                $column = $table->getColumnByOffset($offset);

                if (!$column->getShowFilterButton()) {
                    $objWriter->startElement('filterColumn');
                    $objWriter->writeAttribute('colId', (string) $offset);
                    $objWriter->writeAttribute('hiddenButton', '1');
                    $objWriter->endElement();
                }
            }
            $objWriter->endElement();
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
            $cell = $worksheet->getCellByColumnAndRow($columnIndex, $rangeStart[1]);

            $objWriter->startElement('tableColumn');
            $objWriter->writeAttribute('id', (string) ($offset + 1));
            $objWriter->writeAttribute('name', $table->getShowHeaderRow() ? $cell->getValue() : 'Column' . ($offset + 1));

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
}
