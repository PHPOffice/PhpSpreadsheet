<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RepeatEmptyCellsAndRowsTest extends AbstractFunctional
{
    public function testSaveAndLoadHyperlinks(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $oldSheet = $spreadsheetOld->getActiveSheet();
        $oldSheet->setCellValue('C1', 'xx');
        $oldSheet->setCellValue('G1', 'aa');
        $oldSheet->setCellValue('BB1', 'bb');
        $oldSheet->setCellValue('A6', 'aaa');
        $oldSheet->setCellValue('B7', 'bbb');
        $oldSheet->getRowDimension(10)->setRowHeight(12);
        $oldSheet->setCellValue('A12', 'this is A12');
        $style = $oldSheet->getStyle('B14:D14');
        $style->getFont()->setBold(true);
        $oldSheet->getCell('E15')->setValue('X');
        $oldSheet->mergeCells('E15:G16');
        $oldSheet->getCell('J15')->setValue('j15');
        $oldSheet->getCell('J16')->setValue('j16');
        $oldSheet->getCell('A19')->setValue('lastrow');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('xx', $sheet->getCell('C1')->getValue());
        self::assertSame('aa', $sheet->getCell('G1')->getValue());
        self::assertSame('bb', $sheet->getCell('BB1')->getValue());
        self::assertSame('aaa', $sheet->getCell('A6')->getValue());
        self::assertSame('bbb', $sheet->getCell('B7')->getValue());
        self::assertSame('this is A12', $sheet->getCell('A12')->getValue());
        // Read styles, including row height, not yet implemented for ODS
        self::assertSame('j15', $sheet->getCell('J15')->getValue());
        self::assertSame('j16', $sheet->getCell('J16')->getValue());
        self::assertSame(['E15:G16' => 'E15:G16'], $sheet->getMergeCells());
        self::assertSame('lastrow', $sheet->getCell('A19')->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}
