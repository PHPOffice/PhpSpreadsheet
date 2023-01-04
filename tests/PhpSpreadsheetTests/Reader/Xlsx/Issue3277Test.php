<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue3277Test extends TestCase
{
    public function testIssue3227(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.3277.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);

        $array = $spreadsheet->getActiveSheet()->toArray();

        self::assertSame('data', $array[0][0]);
    }
}
