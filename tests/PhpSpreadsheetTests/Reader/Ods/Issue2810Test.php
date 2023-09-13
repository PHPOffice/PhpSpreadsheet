<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class Issue2810Test extends TestCase
{
    public function testIssue2810(): void
    {
        // Active sheet with title of '0' wasn't found
        $filename = 'tests/data/Reader/Ods/issue.2810.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Active', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
