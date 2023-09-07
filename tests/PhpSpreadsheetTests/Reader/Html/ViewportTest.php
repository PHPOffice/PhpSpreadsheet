<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ViewportTest extends AbstractFunctional
{
    public function testViewport(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $spreadsheetOld->getProperties()->setViewport(Properties::SUGGESTED_VIEWPORT);
        $osheet = $spreadsheetOld->getActiveSheet();
        $osheet->getCell('A1')->setValue(1);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Html');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame('width=device-width, initial-scale=1', $spreadsheet->getProperties()->getViewport());

        $spreadsheet->disconnectWorksheets();
    }

    public function testNoViewport(): void
    {
        $spreadsheetOld = new Spreadsheet();
        //$spreadsheetOld->getProperties()->setViewport(SUGGESTED_VIEWPORT);
        $osheet = $spreadsheetOld->getActiveSheet();
        $osheet->getCell('A1')->setValue(1);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Html');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame('', $spreadsheet->getProperties()->getViewport());

        $spreadsheet->disconnectWorksheets();
    }
}
