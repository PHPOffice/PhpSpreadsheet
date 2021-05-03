<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
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
        $filename = 'tests/data/Reader/Gnumeric/Autofilter_Basic.gnumeric';
        $reader = new Gnumeric();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testAutoFilterRange(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $autoFilterRange = $worksheet->getAutoFilter()->getRange();

        self::assertSame('A1:D57', $autoFilterRange);
    }
}
