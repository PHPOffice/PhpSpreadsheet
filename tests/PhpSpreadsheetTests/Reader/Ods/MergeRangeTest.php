<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class MergeRangeTest extends TestCase
{
    public function testAutoFilterRange(): void
    {
        $filename = 'tests/data/Reader/Ods/MergeRangeTest.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $mergeRanges = $worksheet->getMergeCells();
        self::assertArrayHasKey('B2:C3', $mergeRanges);
        $spreadsheet->disconnectWorksheets();
    }
}
