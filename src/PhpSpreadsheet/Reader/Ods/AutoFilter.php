<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AutoFilter extends BaseReader
{
    protected $spreadsheet;

    protected $tableNs;

    public function __construct(Spreadsheet $spreadsheet, string $tableNs)
    {
        $this->spreadsheet = $spreadsheet;
        $this->tableNs = $tableNs;
    }

    public function readAutoFilters(DOMElement $workbookData): void
    {
        $databases = $workbookData->getElementsByTagNameNS($this->tableNs, 'database-ranges');

        foreach ($databases as $autofilters) {
            foreach ($autofilters->childNodes as $autofilter) {
                $autofilterRange = $autofilter->getAttributeNS($this->tableNs, 'target-range-address');
                $baseAddress = $this->convertToExcelAddressValue($autofilterRange);
                $this->spreadsheet->getActiveSheet()->setAutoFilter($baseAddress);
            }
        }
    }
}
