<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue4128Test extends TestCase
{
    public function testIssue4128(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('A1', $sheet->getActiveCell());
        self::assertSame('A1', $sheet->getSelectedCells());
        $sheet->setCellValue('D1', 'MyDate');
        self::assertSame('A1', $sheet->getActiveCell());
        self::assertSame('A1', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }
}
