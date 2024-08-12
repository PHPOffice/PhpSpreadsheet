<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3807Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3807.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        // rels file points to non-existent theme file
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('Target="theme/theme1.xml/../../../../../../../../1.txt"', $data);
        }
    }

    public function testLoadable(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        // Assert anything to confirm read succeeded
        self::assertSame(1, $sheet->getCell('B1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
