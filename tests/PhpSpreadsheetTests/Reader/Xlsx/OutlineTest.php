<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class OutlineTest extends TestCase
{
    public function testOutline(): void
    {
        $filename = 'tests/data/Reader/XLSX/outline.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(0, $sheet->getRowDimension(1)->getOutlineLevel());
        self::assertSame(2, $sheet->getRowDimension(2)->getOutlineLevel());
        self::assertFalse($sheet->getRowDimension(2)->getCollapsed());
        self::assertTrue($sheet->getRowDimension(2)->getVisible());
        self::assertSame('=SUBTOTAL(9,B2:B4)', $sheet->getCell('B5')->getValue());
        self::assertSame(2, $sheet->getRowDimension(6)->getOutlineLevel());
        self::assertFalse($sheet->getRowDimension(6)->getCollapsed());
        self::assertFalse($sheet->getRowDimension(6)->getVisible());
        self::assertSame(1, $sheet->getRowDimension(8)->getOutlineLevel());
        self::assertTrue($sheet->getRowDimension(8)->getCollapsed());
        self::assertTrue($sheet->getRowDimension(8)->getVisible());
        $fake = new XlsxReader\ColumnAndRowAttributes($sheet, null);
        self::assertFalse($fake->load());
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString('<row r="7" spans="1:2" hidden="true" outlineLevel="2">', $data);
        self::assertStringContainsString('<row r="8" spans="1:2" collapsed="true" outlineLevel="1">', $data);
        $spreadsheet->disconnectWorksheets();
    }
}
