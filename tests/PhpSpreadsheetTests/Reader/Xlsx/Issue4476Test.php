<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Issue4476Test extends TestCase
{
    public function testIssue4476(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.4476.xlsx';

        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $rawValue = $sheet->getCell('A1')->getValue();
        $dt = Date::excelToDateTimeObject($rawValue);

        self::assertSame('2016-06-01 13:37', $dt->format('Y-m-d H:i'));
        $spreadsheet->disconnectWorksheets();
    }
}
