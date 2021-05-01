<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;

class AutoFilter extends BaseReader
{
    public function read(DOMElement $workbookData): void
    {
        $this->readAutoFilters($workbookData);
    }

    protected function readAutoFilters(DOMElement $workbookData): void
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
