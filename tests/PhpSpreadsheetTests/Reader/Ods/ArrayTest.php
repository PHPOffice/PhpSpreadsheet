<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ArrayTest extends AbstractFunctional
{
    public function testSaveAndLoadHyperlinks(): void
    {
        $spreadsheetOld = new Spreadsheet();
        Calculation::getInstance($spreadsheetOld)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->getCell('A1')->setValue('a');
        $sheet->getCell('A2')->setValue('b');
        $sheet->getCell('A3')->setValue('c');
        $sheet->getCell('C1')->setValue(1);
        $sheet->getCell('C2')->setValue(2);
        $sheet->getCell('C3')->setValue(3);
        $sheet->getCell('B1')->setValue('=CONCATENATE(A1:A3,"-",C1:C3)');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame(['t' => 'array', 'ref' => 'B1:B3'], $newSheet->getCell('B1')->getFormulaAttributes());
        self::assertSame('a-1', $newSheet->getCell('B1')->getOldCalculatedValue());
        self::assertSame('b-2', $newSheet->getCell('B2')->getValue());
        self::assertSame('c-3', $newSheet->getCell('B3')->getValue());
        self::assertSame('=CONCATENATE(A1:A3,"-",C1:C3)', $newSheet->getCell('B1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}
