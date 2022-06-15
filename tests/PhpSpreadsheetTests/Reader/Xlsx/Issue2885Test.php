<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue2885Test extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2885.xlsx';

    public function testIssue2885(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('[$-809]0%', $sheet->getStyle('A1')->getNumberFormat()->getFormatCode());

        $finishColumns = $sheet->getHighestColumn();
        $rowsCount = $sheet->getHighestRow();
        $rows = $sheet->rangeToArray("A1:{$finishColumns}{$rowsCount}");
        self::assertSame('8%', $rows[0][0]);

        $spreadsheet->disconnectWorksheets();
    }
}
