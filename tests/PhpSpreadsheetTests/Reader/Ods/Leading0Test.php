<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Leading0Test extends AbstractFunctional
{
    public function testReadAlignmentStyles(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', 3);
        $sheet->setCellValue('A4', 4);
        $sheet->setCellValue('A5', 5);
        $sheet->setCellValue('A6', 6);
        $sheet->setCellValue('A7', 7);
        $sheet->getStyle('A1:A3')->getNumberFormat()
            ->setFormatCode('000000');
        $sheet->getStyle('A4:A6')->getNumberFormat()
            ->setFormatCode('0####');

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['000001'],
            ['000002'],
            ['000003'],
            ['00004'],
            ['00005'],
            ['00006'],
            ['7'],
        ];
        self::assertSame($expected, $newSheet->toArray(formatData: true));
        $expected2 = [
            [1],
            [2],
            [3],
            [4],
            [5],
            [6],
            [7],
        ];
        self::assertSame($expected2, $newSheet->toArray(formatData: false));

        $spreadsheet->disconnectWorksheets();
    }
}
