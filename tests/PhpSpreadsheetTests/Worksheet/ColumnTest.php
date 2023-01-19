<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testInstantiateColumnDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet);
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('A', $columnIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInstantiateColumnSpecified(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet, 'E');
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('E', $columnIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet);
        $cellIterator = $column->getCellIterator();
        self::assertInstanceOf(ColumnCellIterator::class, $cellIterator);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetRowIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet);
        $cellIterator = $column->getRowIterator();
        self::assertInstanceOf(ColumnCellIterator::class, $cellIterator);
        $spreadsheet->disconnectWorksheets();
    }
}
