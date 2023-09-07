<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testLoadTable(): void
    {
        $filename = 'tests/data/Reader/XLSX/tableTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $tables = $worksheet->getTableCollection();
        self::assertCount(1, $tables);

        $table = $tables->offsetGet(0);
        self::assertInstanceOf(Table::class, $table);
        self::assertEquals('SalesData', $table->getName());
        self::assertEquals('A1:G16', $table->getRange());
        self::assertTrue($table->getShowHeaderRow(), 'ShowHeaderRow');
        self::assertTrue($table->getShowTotalsRow(), 'ShowTotalsRow');
        self::assertTrue($table->getAllowFilter(), 'Allow Filter');

        self::assertEquals('Total', $table->getColumn('B')->getTotalsRowLabel());
        self::assertEquals('sum', $table->getColumn('G')->getTotalsRowFunction());
        self::assertEquals('SUM(SalesData[[#This Row],[Q1]:[Q4]])', $table->getColumn('G')->getColumnFormula());

        $tableStyle = $table->getStyle();
        self::assertEquals(TableStyle::TABLE_STYLE_MEDIUM4, $tableStyle->getTheme());
        self::assertTrue($tableStyle->getShowRowStripes(), 'ShowRowStripes');
        self::assertFalse($tableStyle->getShowColumnStripes(), 'ShowColumnStripes');
        self::assertFalse($tableStyle->getShowFirstColumn(), 'ShowFirstColumn');
        self::assertTrue($tableStyle->getShowLastColumn(), 'ShowLastColumn');
    }

    public function testLoadTableNoFilter(): void
    {
        $filename = 'tests/data/Reader/XLSX/TableWithoutFilter.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $tables = $worksheet->getTableCollection();
        self::assertCount(1, $tables);

        $table = $tables->offsetGet(0);
        self::assertInstanceOf(Table::class, $table);
        self::assertFalse($table->getAllowFilter(), 'Allow Filter');
    }
}
