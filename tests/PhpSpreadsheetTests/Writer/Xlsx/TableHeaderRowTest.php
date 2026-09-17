<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

/**
 * Excel reports a workbook whose table covers its header row alone as unreadable, and repairs it
 * by dropping the table. Excel itself never writes one: asked to make a table over a single row of
 * headings it writes the table over the row below as well, leaving that row without a cell. The
 * writer does the same — unless that row already holds something.
 */
class TableHeaderRowTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testHeaderRowTakesTheEmptyRowBelowIt(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([['Year', 'Country']], null, 'A1');
        $sheet->addTable(new Table('A1:B1', 'SalesData'));

        $reloaded = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $worksheet = $reloaded->getActiveSheet();
        $table = $worksheet->getTableByName('SalesData');

        // exactly what Excel writes for a table made over a single row of headings
        self::assertInstanceOf(Table::class, $table);
        self::assertSame('A1:B2', $table->getRange());
        self::assertSame(1, $worksheet->getHighestDataRow(), 'The row taken is left without a cell');

        $reloaded->disconnectWorksheets();
    }

    public function testHeaderRowWillNotSwallowARowThatHoldsSomething(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([['Year', 'Country']], null, 'A1');
        $sheet->getCell('A2')->setValue('Total');
        $sheet->addTable(new Table('A1:B1', 'SalesData'));

        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('needs at least 2 rows');

        $this->writeAndReload($this->spreadsheet, 'Xlsx')->disconnectWorksheets();
    }

    public function testHeaderRowWithOneRowOfDataIsWritten(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([['Year', 'Country'], [2010, 'Belgium']], null, 'A1');
        $sheet->addTable(new Table('A1:B2', 'SalesData'));

        $reloaded = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $table = $reloaded->getActiveSheet()->getTableByName('SalesData');

        self::assertInstanceOf(Table::class, $table);
        self::assertSame('A1:B2', $table->getRange());
        self::assertTrue($table->getShowHeaderRow());

        $reloaded->disconnectWorksheets();
    }

    public function testASingleRowTableWithoutAHeaderRowIsWritten(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([[2010, 'Belgium']], null, 'A1');
        $sheet->addTable((new Table('A1:B1', 'SalesData'))->setShowHeaderRow(false));

        $reloaded = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $table = $reloaded->getActiveSheet()->getTableByName('SalesData');

        self::assertInstanceOf(Table::class, $table);
        self::assertSame('A1:B1', $table->getRange());
        self::assertFalse($table->getShowHeaderRow());

        $reloaded->disconnectWorksheets();
    }
}
