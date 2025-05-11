<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableDxfsStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class TableReader
{
    private Worksheet $worksheet;

    private SimpleXMLElement $tableXml;

    /** @var mixed[]|SimpleXMLElement */
    private $tableAttributes;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $tableXml)
    {
        $this->worksheet = $workSheet;
        $this->tableXml = $tableXml;
    }

    /**
     * Loads Table into the Worksheet.
     *
     * @param TableDxfsStyle[] $tableStyles
     * @param Style[] $dxfs
     */
    public function load(array $tableStyles, array $dxfs): void
    {
        $this->tableAttributes = $this->tableXml->attributes() ?? [];
        // Remove all "$" in the table range
        $tableRange = (string) preg_replace('/\$/', '', $this->tableAttributes['ref'] ?? '');
        if (str_contains($tableRange, ':')) {
            $this->readTable($tableRange, $tableStyles, $dxfs);
        }
    }

    /**
     * Read Table from xml.
     *
     * @param TableDxfsStyle[] $tableStyles
     * @param Style[] $dxfs
     */
    private function readTable(string $tableRange, array $tableStyles, array $dxfs): void
    {
        $table = new Table($tableRange);
        /** @var string[] */
        $attributes = $this->tableAttributes;
        $table->setName((string) ($attributes['displayName'] ?? ''));
        $table->setShowHeaderRow(((string) ($attributes['headerRowCount'] ?? '')) !== '0');
        $table->setShowTotalsRow(((string) ($attributes['totalsRowCount'] ?? '')) === '1');

        $this->readTableAutoFilter($table, $this->tableXml->autoFilter);
        $this->readTableColumns($table, $this->tableXml->tableColumns);
        $this->readTableStyle($table, $this->tableXml->tableStyleInfo, $tableStyles, $dxfs);

        (new AutoFilter($table, $this->tableXml))->load();
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
            /** @var SimpleXMLElement */
            $attributes = $filterColumn->attributes() ?? ['colId' => 0, 'hiddenButton' => 0];
            $column = $table->getColumnByOffset((int) $attributes['colId']);
            $column->setShowFilterButton(((string) $attributes['hiddenButton']) !== '1');
        }
    }

    /**
     * Reads TableColumns from xml.
     */
    private function readTableColumns(Table $table, SimpleXMLElement $tableColumnsXml): void
    {
        $offset = 0;
        foreach ($tableColumnsXml->tableColumn as $tableColumn) {
            /** @var SimpleXMLElement */
            $attributes = $tableColumn->attributes() ?? ['totalsRowLabel' => 0, 'totalsRowFunction' => 0];
            $column = $table->getColumnByOffset($offset++);

            if ($table->getShowTotalsRow()) {
                if ($attributes['totalsRowLabel']) {
                    $column->setTotalsRowLabel((string) $attributes['totalsRowLabel']);
                }

                if ($attributes['totalsRowFunction']) {
                    $column->setTotalsRowFunction((string) $attributes['totalsRowFunction']);
                }
            }

            if ($tableColumn->calculatedColumnFormula) {
                $column->setColumnFormula((string) $tableColumn->calculatedColumnFormula);
            }
        }
    }

    /**
     * Reads TableStyle from xml.
     *
     * @param TableDxfsStyle[] $tableStyles
     * @param Style[] $dxfs
     */
    private function readTableStyle(Table $table, SimpleXMLElement $tableStyleInfoXml, array $tableStyles, array $dxfs): void
    {
        $tableStyle = new TableStyle();
        $attributes = $tableStyleInfoXml->attributes();
        if ($attributes !== null) {
            $tableStyle->setTheme((string) $attributes['name']);
            $tableStyle->setShowRowStripes((string) $attributes['showRowStripes'] === '1');
            $tableStyle->setShowColumnStripes((string) $attributes['showColumnStripes'] === '1');
            $tableStyle->setShowFirstColumn((string) $attributes['showFirstColumn'] === '1');
            $tableStyle->setShowLastColumn((string) $attributes['showLastColumn'] === '1');

            foreach ($tableStyles as $style) {
                if ($style->getName() === (string) $attributes['name']) {
                    $tableStyle->setTableDxfsStyle($style, $dxfs);
                }
            }
        }
        $table->setStyle($tableStyle);
    }
}
