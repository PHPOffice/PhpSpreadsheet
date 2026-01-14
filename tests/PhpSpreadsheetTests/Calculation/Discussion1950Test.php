<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Discussion1950Test extends TestCase
{
    public function testMultipleUnions(): void
    {
        $infile = 'tests/data/Reader/Xlsx/discussion.1950.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=SUM((A1,A2),(B1,B2))', $sheet->getCell('A5')->getValue());
        //$sheet->getCell('A5')->setValue('=SUM((A1∪A2)∪(B1∪B2)))');
        self::assertSame(10, $sheet->getCell('A5')->getCalculatedValue(), 'error out so use old calculated value');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnexpectedUnion(): void
    {
        // was failing in a different manner than prior test
        $infile = 'tests/data/Reader/Xlsx/issue.4656.d.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=RANK(A1,(A2,A3,A4))', $sheet->getCell('A5')->getValue());
        //$sheet->getCell('A5')->setValue('=RANK(A1,(A2∪A3∪A4))');
        self::assertSame(2, $sheet->getCell('A5')->getCalculatedValue(), 'error out so use old calculated value');
        $spreadsheet->disconnectWorksheets();
    }
}
