<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class OctothorpeTest extends TestCase
{
    public function testOctothorpeInName(): void
    {
        // Permit # in file name.
        $filename = 'tests/data/Reader/XLSX/octo#thorpe.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('xyz', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
