<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4375Test extends TestCase
{
    private static string $file = 'tests/data/Reader/XLSX/issue.4375.small.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$file;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file) ?: '';
        $expected = '<ignoredErrors><ignoredError sqref="A2:B5 B1:F1" numberStoredAsText="1"/></ignoredErrors>';
        self::assertStringContainsString($expected, $data);
    }

    public function testDataOnly(): void
    {
        $file = self::$file;
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('0', $sheet->getCell('A2')->getValue());
        self::assertFalse(
            $sheet->getCell('A2')
                ->getIgnoredErrors()
                ->getNumberStoredAsText()
        );
        self::assertFalse($sheet->cellExists('A3'));
        $spreadsheet->disconnectWorksheets();
    }

    public function testNormalRead(): void
    {
        $file = self::$file;
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('0', $sheet->getCell('A2')->getValue());
        self::assertTrue(
            $sheet->getCell('A2')
                ->getIgnoredErrors()
                ->getNumberStoredAsText()
        );
        self::assertFalse($sheet->cellExists('A3'));
        $spreadsheet->disconnectWorksheets();
    }
}
