<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class AutoSizeTest extends TestCase
{
    protected Spreadsheet $spreadsheet;

    protected Worksheet $worksheet;

    protected function setUp(): void
    {
        parent::setUp();

        $spreadsheet = new Spreadsheet();
        $this->worksheet = $spreadsheet->getActiveSheet();

        $this->worksheet->setCellValue('A1', 'YEAR')
            ->setCellValue('B1', 'QUARTER')
            ->setCellValue('C1', 'COUNTRY')
            ->setCellValue('D1', 'SALES');
        $dataArray = [
            ['10', 'Q1', 'United States', 790],
            ['10', 'Q2', 'United States', 730],
            ['10', 'Q3', 'United States', 860],
            ['10', 'Q4', 'United States', 850],
        ];

        $this->worksheet->fromArray($dataArray, null, 'A2');

        $toColumn = $this->worksheet->getHighestColumn();
        ++$toColumn;
        for ($i = 'A'; $i !== $toColumn; ++$i) {
            $this->worksheet->getColumnDimension($i)->setAutoSize(true);
        }
    }

    protected function setTable(): Table
    {
        $table = new Table('A1:D5', 'Sales_Data');
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $table->setStyle($tableStyle);
        $this->worksheet->addTable($table);

        return $table;
    }

    protected function readColumnSizes(): array
    {
        $columnSizes = [];
        $toColumn = $this->worksheet->getHighestColumn();
        ++$toColumn;
        for ($column = 'A'; $column !== $toColumn; ++$column) {
            $columnSizes[$column] = $this->worksheet->getColumnDimension($column)->getWidth();
        }

        return $columnSizes;
    }

    public function testStandardAutoSize(): void
    {
        $this->worksheet->calculateColumnWidths();
        $columnSizes = $this->readColumnSizes();

        self::assertSame(['A' => 5.856, 'B' => 9.283, 'C' => 16.425, 'D' => 6.998], $columnSizes);
    }

    public function testAutoFilterAutoSize(): void
    {
        $this->worksheet->setAutoFilter('A1:D5');

        $this->worksheet->calculateColumnWidths();
        $columnSizes = $this->readColumnSizes();

        self::assertSame(['A' => 8.141, 'B' => 11.569, 'C' => 16.425, 'D' => 9.283], $columnSizes);
    }

    public function testTableWithAutoFilterAutoSize(): void
    {
        $this->setTable();

        $this->worksheet->calculateColumnWidths();
        $columnSizes = $this->readColumnSizes();

        self::assertSame(['A' => 8.141, 'B' => 11.569, 'C' => 16.425, 'D' => 9.283], $columnSizes);
    }

    public function testTableWithoutHiddenHeadersAutoSize(): void
    {
        $table = $this->setTable();
        $table->setShowHeaderRow(false);

        $this->worksheet->calculateColumnWidths();
        $columnSizes = $this->readColumnSizes();

        self::assertSame(['A' => 5.856, 'B' => 9.283, 'C' => 16.425, 'D' => 6.998], $columnSizes);
    }

    public function testTableWithAutoFilterCenterHeaders(): void
    {
        $this->setTable();
        $this->worksheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $this->worksheet->calculateColumnWidths();
        $columnSizes = $this->readColumnSizes();

        self::assertSame(['A' => 10.569, 'B' => 13.997, 'C' => 16.425, 'D' => 11.711], $columnSizes);
    }
}
