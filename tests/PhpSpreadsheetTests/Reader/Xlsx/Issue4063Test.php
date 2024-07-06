<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue4063Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.4063.xlsx';

    public function testSharedStringsWithEmptyString(): void
    {
        $spreadsheet = IOFactory::load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        $nbsp = "\u{00a0}";
        self::assertSame(['A' => '226', 'B' => '', 'C' => $nbsp], $data[17]);
        self::assertSame(['A' => '38873', 'B' => 'gg', 'C' => ' '], $data[22]);
        $spreadsheet->disconnectWorksheets();
    }
}
