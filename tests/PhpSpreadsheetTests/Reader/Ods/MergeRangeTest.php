<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class MergeRangeTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        $filename = 'tests/data/Reader/Ods/MergeRangeTest.ods';
        $reader = new Ods();
        $this->spreadsheet = $reader->load($filename);
    }

    public function testAutoFilterRange(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $mergeRanges = $worksheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);
    }
}
