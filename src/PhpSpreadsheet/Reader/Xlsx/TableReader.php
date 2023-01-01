<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class TableReader
{
    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @var SimpleXMLElement
     */
    private $tableXml;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $tableXml)
    {
        $this->worksheet = $workSheet;
        $this->tableXml = $tableXml;
    }

    /**
     * Loads Table into the Worksheet.
     */
    public function load(): void
    {
        // Remove all "$" in the table range
        $tableRange = (string) preg_replace('/\$/', '', $this->tableXml['ref'] ?? '');
        if (strpos($tableRange, ':') !== false) {
            $this->readTable($tableRange, $this->tableXml);
        }
    }

    /**
     * Read Table from xml.
     */
    private function readTable(string $tableRange, SimpleXMLElement $tableXml): void
    {
        $table = new Table($tableRange);
        $table->setName((string) $tableXml['displayName']);
        $table->setShowHeaderRow((string) $tableXml['headerRowCount'] !== '0');
        $table->setShowTotalsRow((string) $tableXml['totalsRowCount'] === '1');

        $this->readTableAutoFilter($table, $tableXml->autoFilter);
        $this->readTableColumns($table, $tableXml->tableColumns);
        $this->readTableStyle($table, $tableXml->tableStyleInfo);

        (new AutoFilter($table, $tableXml))->load();
        $this->worksheet->addTable($table);
    }

    /**
     * Reads TableAutoFilter from xml.
     */
    private function readTableAutoFilter(Table $table, SimpleXMLElement $autoFilterXml): void
    {
        if ($autoFilterXml->filterColumn === null) {
            $table->setAllowFilter(false);

            return;
        }

        foreach ($autoFilterXml->filterColumn as $filterColumn) {
            $column = $table->getColumnByOffset((int) $filterColumn['colId']);
            $column->setShowFilterButton((string) $filterColumn['hiddenButton'] !== '1');
        }
    }

    /**
     * Reads TableColumns from xml.
     */
    private function readTableColumns(Table $table, SimpleXMLElement $tableColumnsXml): void
    {
        $offset = 0;
        foreach ($tableColumnsXml->tableColumn as $tableColumn) {
            $column = $table->getColumnByOffset($offset++);

            if ($table->getShowTotalsRow()) {
                if ($tableColumn['totalsRowLabel']) {
                    $column->setTotalsRowLabel((string) $tableColumn['totalsRowLabel']);
                }

                if ($tableColumn['totalsRowFunction']) {
                    $column->setTotalsRowFunction((string) $tableColumn['totalsRowFunction']);
                }
            }

            if ($tableColumn->calculatedColumnFormula) {
                $column->setColumnFormula((string) $tableColumn->calculatedColumnFormula);
            }
        }
    }

    /**
     * Reads TableStyle from xml.
     */
    private function readTableStyle(Table $table, SimpleXMLElement $tableStyleInfoXml): void
    {
        $tableStyle = new TableStyle();
        $tableStyle->setTheme((string) $tableStyleInfoXml['name']);
        $tableStyle->setShowRowStripes((string) $tableStyleInfoXml['showRowStripes'] === '1');
        $tableStyle->setShowColumnStripes((string) $tableStyleInfoXml['showColumnStripes'] === '1');
        $tableStyle->setShowFirstColumn((string) $tableStyleInfoXml['showFirstColumn'] === '1');
        $tableStyle->setShowLastColumn((string) $tableStyleInfoXml['showLastColumn'] === '1');
        $table->setStyle($tableStyle);
    }
}
