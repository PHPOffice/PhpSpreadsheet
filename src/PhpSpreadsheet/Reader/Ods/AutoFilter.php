<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;
use DOMNode;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilter extends BaseLoader
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
                $autofilterRange = $this->getAttributeValue($autofilter, 'target-range-address');
                if ($autofilterRange !== null) {
                    $baseAddress = FormulaTranslator::convertToExcelAddressValue($autofilterRange);
                    if ($baseAddress === '') {
                        continue;
                    }
                    [$title, $range] = Worksheet::extractSheetTitle($baseAddress, true, true);
                    $range = str_replace('$', '', $range);
                    // A sheet that does not exist, or that was not loaded, takes nothing of it
                    $worksheet = $title === '' ? $this->spreadsheet->getActiveSheet() : $this->spreadsheet->getSheetByName($title);
                    if ($worksheet === null) {
                        continue;
                    }
                    if (!$this->readTable($autofilter, $worksheet, $range)) {
                        $worksheet->setAutoFilter($range);
                    }
                }
            }
        }
    }

    /**
     * A named database range is a table, as LibreOffice writes one; a range with no name
     * or an anonymous one of LibreOffice (the filter of a sheet) is not. As in LibreOffice,
     * a range whose name cannot be a table, or is taken already, is dropped.
     */
    private function readTable(DOMNode $databaseRange, Worksheet $worksheet, string $range): bool
    {
        $name = (string) $this->getAttributeValue($databaseRange, 'name');
        if ($name === '' || str_starts_with($name, '__Anonymous_Sheet_DB__') || str_starts_with($name, '__Anonymous_DB__')) {
            return false;
        }

        try {
            $table = new Table($range, $name);
            $table->setShowHeaderRow($this->getAttributeValue($databaseRange, 'contains-header') !== 'false');
            $table->setAllowFilter($this->getAttributeValue($databaseRange, 'display-filter-buttons') === 'true');
            $worksheet->addTable($table);
        } catch (PhpSpreadsheetException) {
        }

        return true;
    }

    protected function getAttributeValue(?DOMNode $node, string $attributeName): ?string
    {
        if ($node !== null && $node->attributes !== null) {
            $attribute = $node->attributes->getNamedItemNS(
                $this->tableNs,
                $attributeName
            );

            if ($attribute !== null) {
                return $attribute->nodeValue;
            }
        }

        return null;
    }
}
