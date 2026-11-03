<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilters
{
    private XMLWriter $objWriter;

    private Spreadsheet $spreadsheet;

    public function __construct(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        $this->objWriter = $objWriter;
        $this->spreadsheet = $spreadsheet;
    }

    public function write(): void
    {
        $wrapperWritten = false;
        $tableCount = 0;
        $sheetCount = $this->spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $worksheet = $this->spreadsheet->getSheet($i);
            $autofilter = $worksheet->getAutoFilter();
            if (!empty($autofilter->getRange())) {
                if ($wrapperWritten === false) {
                    $this->objWriter->startElement('table:database-ranges');
                    $wrapperWritten = true;
                }
                $this->objWriter->startElement('table:database-range');
                $this->objWriter->writeAttribute('table:orientation', 'column');
                $this->objWriter->writeAttribute('table:display-filter-buttons', 'true');
                $this->objWriter->writeAttribute(
                    'table:target-range-address',
                    $this->formatRange($worksheet, $autofilter)
                );
                $this->objWriter->endElement();
            }

            // A table is a named database range, as LibreOffice writes it; the header row
            // it contains is what LibreOffice tags as the header cells of a tagged PDF
            foreach ($worksheet->getTableCollection() as $table) {
                $name = $table->getName();
                if ($name === '') {
                    do {
                        $name = 'Table' . ++$tableCount;
                    } while ($this->spreadsheet->getTableByName($name) !== null);
                }
                if ($wrapperWritten === false) {
                    $this->objWriter->startElement('table:database-ranges');
                    $wrapperWritten = true;
                }
                $this->objWriter->startElement('table:database-range');
                $this->objWriter->writeAttribute('table:name', $name);
                $this->objWriter->writeAttribute('table:target-range-address', $this->formatTableRange($worksheet, $table));
                if (!$table->getShowHeaderRow()) {
                    $this->objWriter->writeAttribute('table:contains-header', 'false');
                } elseif ($table->getAllowFilter()) {
                    $this->objWriter->writeAttribute('table:display-filter-buttons', 'true');
                }
                $this->objWriter->endElement();
            }
        }

        if ($wrapperWritten === true) {
            $this->objWriter->endElement();
        }
    }

    protected function formatRange(Worksheet $worksheet, AutoFilter $autofilter): string
    {
        $title = $worksheet->getTitle();
        $range = $autofilter->getRange();

        return "'{$title}'.{$range}";
    }

    private function formatTableRange(Worksheet $worksheet, Table $table): string
    {
        $sheet = "'" . str_replace("'", "''", $worksheet->getTitle()) . "'";
        [$start, $end] = explode(':', $table->getRange());

        return "{$sheet}.{$start}:{$sheet}.{$end}";
    }
}
