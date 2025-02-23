<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testInstantiateRowDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet);
        $rowIndex = $row->getRowIndex();
        self::assertEquals(1, $rowIndex);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInstantiateRowSpecified(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet, 5);
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
        self::assertSame(1, $cellIterator->getCurrentColumnIndex());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetColumnIterator(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = new Row($sheet);
        $cellIterator = $row->getColumnIterator();
        self::assertSame(1, $cellIterator->getCurrentColumnIndex());
        $spreadsheet->disconnectWorksheets();
    }
}
