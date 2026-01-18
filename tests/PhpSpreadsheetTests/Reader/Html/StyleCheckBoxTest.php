<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class StyleCheckBoxTest extends AbstractFunctional
{
    public function testStyleCheckBox(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $sheetOld->setCellValue('A1', true);
        $sheetOld->setCellValue('A2', false);
        $sheetOld->setCellValue('A3', false); // no checkbox
        $sheetOld->getStyle('A1')->setCheckBox(true);
        $sheetOld->getStyle('A2')->setCheckBox(true);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Html');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('A1')->getValue());
        self::assertFalse($sheet->getCell('A2')->getValue());
        self::assertFalse($sheet->getCell('A3')->getValue());
        self::assertTrue($sheet->getStyle('A1')->getCheckBox());
        self::assertTrue($sheet->getStyle('A2')->getCheckBox());
        self::assertFalse($sheet->getStyle('A3')->getCheckBox());

        $spreadsheet->disconnectWorksheets();
    }

    private function writeNoPreCalc(HtmlWriter $writer): void
    {
        $writer->setPreCalculateFormulas(false);
    }

    public function testStyleCheckBoxNoPreCalculate(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $sheetOld->setCellValue('A1', true);
        $sheetOld->setCellValue('A2', false);
        $sheetOld->setCellValue('A3', false); // no checkbox
        $sheetOld->getStyle('A1')->setCheckBox(true);
        $sheetOld->getStyle('A2')->setCheckBox(true);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Html', null, $this->writeNoPreCalc(...));
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getCell('A1')->getValue());
        self::assertFalse($sheet->getCell('A2')->getValue());
        self::assertFalse($sheet->getCell('A3')->getValue());
        self::assertTrue($sheet->getStyle('A1')->getCheckBox());
        self::assertTrue($sheet->getStyle('A2')->getCheckBox());
        self::assertFalse($sheet->getStyle('A3')->getCheckBox());

        $spreadsheet->disconnectWorksheets();
    }
}
