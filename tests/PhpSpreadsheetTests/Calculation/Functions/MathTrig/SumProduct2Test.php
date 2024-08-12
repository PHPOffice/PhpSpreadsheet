<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class SumProduct2Test extends AllSetupTeardown
{
    public function testSUMPRODUCT(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.3909b.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=SUMPRODUCT(((calNames=I3)*(calTiers=$K$2))*calHours)', $sheet->getCell('K3')->getValue());
        self::assertSame(40, $sheet->getCell('K3')->getCalculatedValue());
        self::assertSame(4, $sheet->getCell('L3')->getCalculatedValue());
        self::assertSame(40, $sheet->getCell('M3')->getCalculatedValue());
        self::assertSame(4, $sheet->getCell('N3')->getCalculatedValue());
        self::assertSame(40, $sheet->getCell('K4')->getCalculatedValue());
        self::assertSame(0, $sheet->getCell('L4')->getCalculatedValue());
        self::assertSame(40, $sheet->getCell('M4')->getCalculatedValue());
        self::assertSame(0, $sheet->getCell('N4')->getCalculatedValue());
        self::assertSame(24, $sheet->getCell('K5')->getCalculatedValue());
        self::assertSame(0, $sheet->getCell('L5')->getCalculatedValue());
        self::assertSame(24, $sheet->getCell('M5')->getCalculatedValue());
        self::assertSame(0, $sheet->getCell('N5')->getCalculatedValue());
        self::assertSame('=SUMPRODUCT(calHours*((calNames=I3)*(calTiers=$K$2)))', $sheet->getCell('I14')->getValue());
        self::assertSame(40, $sheet->getCell('I14')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
