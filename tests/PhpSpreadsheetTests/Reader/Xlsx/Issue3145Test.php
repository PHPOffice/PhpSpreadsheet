<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue3145Test extends TestCase
{
    public function testIssue3145(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.3145.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertEquals('Headline A', $sheet->getCell('A1')->getValue());
        self::assertEquals('Configdential B', $sheet->getCell('A2')->getValue());
        self::assertSame('OFFSET(INDIRECT(SUBSTITUTE($A2," ","")),0,0,COUNTA(INDIRECT(SUBSTITUTE($A2," ","")&"Col")),1)', $sheet->getCell('B2')->getDataValidation()->getFormula1());

        $spreadsheet->disconnectWorksheets();
    }
}
