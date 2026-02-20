<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Issue4476Test extends TestCase
{
    public function testIssue2810(): void
    {
        // Active sheet with title of '0' wasn't found
        $filename = 'tests/data/Reader/Ods/issue.4476.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $rawValue = $sheet->getCell('A1')->getValue();
        $dt = Date::excelToDateTimeObject($rawValue);
        self::assertSame('2016-06-01 13:37', $dt->format('Y-m-d H:i'));
        $spreadsheet->disconnectWorksheets();
    }
}
