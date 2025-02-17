<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testInstantiateColumnDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('A', $columnIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInstantiateColumnSpecified(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet, 'E');
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
        self::assertSame(1, $cellIterator->key());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetRowIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = new Column($sheet);
        $cellIterator = $column->getRowIterator();
        self::assertSame(1, $cellIterator->key());
        $spreadsheet->disconnectWorksheets();
    }
}
