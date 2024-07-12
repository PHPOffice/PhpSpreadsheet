<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue2581Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.2581.xlsx';

    public function testIssue2581(): void
    {
        // CELL function (unimplemented) embedded in another function
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=CONCATENATE("Prefix ",MID(CELL("filename"),FIND("]",CELL("filename"))+1,255), " Suffix")', $sheet->getCell('B1')->getValue());
        self::assertSame('Prefix SomeName Suffix', $sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
