<?php

namespace PhpOffice\PhpSpreadsheetTests\Features\AutoFilter\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class BasicLoadTest extends TestCase
{
    public function testLoadAutoFilter()
    {
        $filename = 'tests/data/Features/AutoFilter/Xlsx/AutoFilter_Basic.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame('A1:D57', $worksheet->getAutoFilter()->getRange());
    }

    public function testLoadOffice365AutoFilter()
    {
        $filename = 'tests/data/Features/AutoFilter/Xlsx/AutoFilter_Basic_Office365.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        self::assertSame('A1:D57', $worksheet->getAutoFilter()->getRange());
    }
}
