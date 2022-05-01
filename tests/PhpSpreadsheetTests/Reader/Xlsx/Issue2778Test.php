<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue2778Test extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2778.xlsx';

    public function testIssue2778(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(6, $sheet->getCell('D1')->getCalculatedValue());
        self::assertSame(63, $sheet->getCell('F1')->getCalculatedValue());
        self::assertSame(10, $sheet->getCell('C10')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
