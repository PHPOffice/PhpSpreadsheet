<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class AbsolutePathTest extends TestCase
{
    public static function testPr1869(): void
    {
        $xlsxFile = 'tests/data/Reader/XLSX/pr1769e.xlsx';
        $reader = new Xlsx();
        $result = $reader->listWorksheetInfo($xlsxFile);

        self::assertIsArray($result);
        self::assertEquals(3, $result[0]['totalRows']);
    }
}
