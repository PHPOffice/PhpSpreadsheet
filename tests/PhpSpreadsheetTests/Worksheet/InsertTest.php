<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class InsertTest extends TestCase
{
    public function testInsertRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 2, 3, 4],
            [5, 6, 7, 8],
            [9, 10, 11, 12],
            [13, 14, 15, 16],
            [17, 18, 19, 20],
        ]);
        $sheet->getRowDimension(1000)->setVisible(false);
        $sheet->getStyle('C3')->getFont()->setBold(true);
        self::assertSame(1000, $sheet->getHighestRow());
        self::assertSame(5, $sheet->getHighestDataRow());
        $currentRow = 4;
        $sheet->insertNewRowBefore($currentRow, 1);
        self::assertSame(1001, $sheet->getHighestRow());
        self::assertSame(6, $sheet->getHighestDataRow());
        self::assertTrue($sheet->getStyle('C3')->getFont()->getBold());
        self::assertSame(11, $sheet->getCell('C3')->getValue());
        self::assertTrue($sheet->getStyle('C4')->getFont()->getBold());
        self::assertNull($sheet->getCell('C4')->getValue());
        self::assertFalse($sheet->getRowDimension(1001)->getVisible());
        self::assertTrue($sheet->getRowDimension(1000)->getVisible());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 2, 3, 4],
            [5, 6, 7, 8],
            [9, 10, 11, 12],
            [13, 14, 15, 16],
            [17, 18, 19, 20],
        ]);
        $sheet->getColumnDimension('ZY')->setVisible(false);
        $sheet->getStyle('C3')->getFont()->setBold(true);
        self::assertSame('ZY', $sheet->getHighestColumn());
        self::assertSame('D', $sheet->getHighestDataColumn());
        $currentColumn = 'D';
        $sheet->insertNewColumnBefore($currentColumn, 1);
        self::assertSame('ZZ', $sheet->getHighestColumn());
        self::assertSame('E', $sheet->getHighestDataColumn());
        self::assertTrue($sheet->getStyle('C3')->getFont()->getBold());
        self::assertSame(11, $sheet->getCell('C3')->getValue());
        self::assertTrue($sheet->getStyle('D3')->getFont()->getBold());
        self::assertNull($sheet->getCell('D3')->getValue());
        self::assertFalse($sheet->getColumnDimension('ZZ')->getVisible());
        self::assertTrue($sheet->getColumnDimension('ZY')->getVisible());
        $spreadsheet->disconnectWorksheets();
    }
}
