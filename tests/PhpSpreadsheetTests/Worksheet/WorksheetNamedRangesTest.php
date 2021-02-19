<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class WorksheetNamedRangesTest extends TestCase
{
    protected $spreadsheet;

    protected function setUp(): void
    {
        Settings::setLibXmlLoaderOptions(null); // reset to default options

        $reader = new Xlsx();
        $this->spreadsheet = $reader->load('tests/data/Worksheet/namedRangeTest.xlsx');
    }

    public function testCellExists(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists('GREETING');
        self::assertTrue($cellExists);
    }

    public function testGetCell(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell('GREETING');
        self::assertSame('Hello', $cell->getValue());
    }

    public function testNamedRangeToArray(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $rangeData = $worksheet->namedRangeToArray('Range1');
        self::assertSame([[1, 2, 3]], $rangeData);
    }
}
