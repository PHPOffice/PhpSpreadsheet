<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class NumberFormatGeneralTest extends AbstractFunctional
{
    public function testGeneral(): void
    {
        $filename = 'tests/data/Reader/XLS/issue2239.xls';
        $contents = file_get_contents($filename) ?: '';
        self::assertStringContainsString('GENERAL', $contents);

        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Blad1');
        $array = $sheet->toArray();
        self::assertSame('€ 2.95', $array[1][3]);
        self::assertSame(2.95, $sheet->getCell('D2')->getValue());
        self::assertSame(2.95, $sheet->getCell('D2')->getCalculatedValue());
        self::assertSame('€ 2.95', $sheet->getCell('D2')->getFormattedValue());
        self::assertSame('21', $array[1][4]);
        self::assertSame(21, $sheet->getCell('E2')->getValue());
        self::assertSame(21, $sheet->getCell('E2')->getCalculatedValue());
        self::assertSame('21', $sheet->getCell('E2')->getFormattedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
