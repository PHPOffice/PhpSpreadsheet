<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testInstantiateRowDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet);
        self::assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        self::assertEquals(1, $rowIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInstantiateRowSpecified(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet, 5);
        self::assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        self::assertEquals(5, $rowIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet);
        $cellIterator = $row->getCellIterator();
        self::assertInstanceOf(RowCellIterator::class, $cellIterator);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetColumnIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet);
        $cellIterator = $row->getColumnIterator();
        self::assertInstanceOf(RowCellIterator::class, $cellIterator);
        $spreadsheet->disconnectWorksheets();
    }
}
