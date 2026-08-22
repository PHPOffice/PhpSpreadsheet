<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue1637Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.1637.xlsx';

    public function testXludf(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(
            '=IFERROR(__xludf.DUMMYFUNCTION("flatten(A1:A5, B1:B5)"),1.0)',
            $sheet->getCell('C1')->getValue()
        );
        self::assertSame(1.0, $sheet->getCell('C1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
