<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ReadDynamTest extends AbstractFunctional
{
    public function writeCse(XlsxWriter $writer): void
    {
        $writer->setUseCSEArrays(true);
    }

    public function testCse(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $calcOld = Calculation::getInstance($spreadsheetOld);
        $calcOld->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );
        $sheetOld->fromArray(
            [1, 2, 2, 4, 3, 2, 1, 3, 3, 3, 5],
            null,
            'A14',
            true
        );
        $sheetOld->setCellValue('A15', '=UNIQUE(A14:K14, TRUE)');
        /** @var callable */
        $callableWriter = [$this, 'writeCse'];
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx', null, $callableWriter);
        $spreadsheetOld->disconnectWorksheets();
        $calc = Calculation::getInstance($spreadsheet);
        self::assertSame(
            Calculation::RETURN_ARRAY_AS_VALUE,
            $calc->getInstanceArrayReturnType()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testDynam(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $calcOld = Calculation::getInstance($spreadsheetOld);
        $calcOld->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );
        $sheetOld->fromArray(
            [1, 2, 2, 4, 3, 2, 1, 3, 3, 3, 5],
            null,
            'A14',
            true
        );
        $sheetOld->setCellValue('A15', '=UNIQUE(A14:K14, TRUE)');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheetOld->disconnectWorksheets();
        $calc = Calculation::getInstance($spreadsheet);
        self::assertSame(
            Calculation::RETURN_ARRAY_AS_ARRAY,
            $calc->getInstanceArrayReturnType()
        );
        $spreadsheet->disconnectWorksheets();
    }
}
