<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class WholeRowAndColumnTest extends TestCase
{
    /**
     * Test that selection uses PhpSpreadsheet limits, not Xls limits.
     */
    public function testSelectedRows(): void
    {
        $filename = 'tests/data/Reader/XLS/WholeRowAndColumn.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet1 = $spreadsheet->getSheetByName('Sheet1');
        self::assertNotNull($sheet1);
        self::assertSame('B1:B1048576', $sheet1->getSelectedCells());
        $sheet2 = $spreadsheet->getSheetByName('Sheet2');
        self::assertNotNull($sheet2);
        self::assertSame('A2:XFD2', $sheet2->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }
}
