<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DefinedNames extends BaseReader
{
    protected $spreadsheet;

    protected $tableNs;

    public function __construct(Spreadsheet $spreadsheet, string $tableNs)
    {
        $this->spreadsheet = $spreadsheet;
        $this->tableNs = $tableNs;
    }

    /**
     * Read any Named Ranges that are defined in this spreadsheet.
     */
    public function readDefinedRanges(DOMElement $workbookData): void
    {
        $namedRanges = $workbookData->getElementsByTagNameNS($this->tableNs, 'named-range');
        foreach ($namedRanges as $definedNameElement) {
            $definedName = $definedNameElement->getAttributeNS($this->tableNs, 'name');
            $baseAddress = $definedNameElement->getAttributeNS($this->tableNs, 'base-cell-address');
            $range = $definedNameElement->getAttributeNS($this->tableNs, 'cell-range-address');

            $baseAddress = $this->convertToExcelAddressValue($baseAddress);
            $range = $this->convertToExcelAddressValue($range);

            $this->addDefinedName($baseAddress, $definedName, $range);
        }
    }

    /**
     * Read any Named Formulae that are defined in this spreadsheet.
     */
    public function readDefinedExpressions(DOMElement $workbookData): void
    {
        $namedExpressions = $workbookData->getElementsByTagNameNS($this->tableNs, 'named-expression');
        foreach ($namedExpressions as $definedNameElement) {
            $definedName = $definedNameElement->getAttributeNS($this->tableNs, 'name');
            $baseAddress = $definedNameElement->getAttributeNS($this->tableNs, 'base-cell-address');
            $expression = $definedNameElement->getAttributeNS($this->tableNs, 'expression');

            $baseAddress = $this->convertToExcelAddressValue($baseAddress);
            $expression = $this->convertToExcelFormulaValue($expression);

            $this->addDefinedName($baseAddress, $definedName, $expression);
        }
    }

    /**
     * Assess scope and store the Defined Name.
     */
    private function addDefinedName(string $baseAddress, string $definedName, string $value): void
    {
        [$sheetReference] = Worksheet::extractSheetTitle($baseAddress, true);
        $worksheet = $this->spreadsheet->getSheetByName($sheetReference);
        // Worksheet might still be null if we're only loading selected sheets rather than the full spreadsheet
        if ($worksheet !== null) {
            $this->spreadsheet->addDefinedName(DefinedName::createInstance((string) $definedName, $worksheet, $value));
        }
    }
}
