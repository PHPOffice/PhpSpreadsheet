<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class AutoFilterTest extends TestCase
{
    public function testAutoFilterRange(): void
    {
        $filename = 'tests/data/Reader/Ods/AutoFilter.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $autoFilterRange = $worksheet->getAutoFilter()->getRange();

        self::assertSame('A1:C9', $autoFilterRange);
        $spreadsheet->disconnectWorksheets();
    }
}
