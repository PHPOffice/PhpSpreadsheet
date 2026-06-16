<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue1203Test extends TestCase
{
    public static function testCopyFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A5', 5);
        $sheet->setCellValue('E5', '=A5+$A$1');
        $sheet->insertNewRowBefore(5, 1);
        $e5 = $sheet->getCell('E5')->getValue();
        self::assertNull($e5);
        self::assertSame('=A6+$A$1', $sheet->getCell('E6')->getValue());
        $sheet->copyFormula('E6', 'E5');
        self::assertSame('=A5+$A$1', $sheet->getCell('E5')->getValue());
        $sheet->copyFormula('E6', 'H9');
        self::assertSame('=D9+$A$1', $sheet->getCell('H9')->getValue());
        $sheet->copyFormula('A6', 'Z9');
        self::assertSame(5, $sheet->getCell('Z9')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
