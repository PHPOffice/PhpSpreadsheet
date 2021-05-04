<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AutoFilterTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/Ods/AutoFilter.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testAutoFilterRange(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $autoFilterRange = $worksheet->getAutoFilter()->getRange();

        self::assertSame('A1:C9', $autoFilterRange);
    }
}
