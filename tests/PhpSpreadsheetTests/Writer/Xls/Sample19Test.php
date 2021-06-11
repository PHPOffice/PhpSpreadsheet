<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Sample19Test extends AbstractFunctional
{
    public function testSample19Xls(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Firstname:')
            ->setCellValue('A2', 'Lastname:')
            ->setCellValue('A3', 'Fullname:')
            ->setCellValue('B1', 'Maarten')
            ->setCellValue('B2', 'Balliauw')
            ->setCellValue('B3', '=B1 & " " & B2')
            ->setCellValue('C1', '=A2&A3&A3&A2&B1');

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $sheet0 = $robj->setActiveSheetIndex(0);
        // Xls parser eliminates unneeded whitespace
        self::assertEquals('=B1&" "&B2', $sheet0->getCell('B3')->getValue());
        self::assertEquals('Maarten Balliauw', $sheet0->getCell('B3')->getCalculatedValue());
        self::assertEquals('Lastname:Fullname:Fullname:Lastname:Maarten', $sheet0->getCell('C1')->getCalculatedValue());
    }
}
