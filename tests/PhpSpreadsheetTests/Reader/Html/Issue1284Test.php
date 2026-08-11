<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue1284Test extends AbstractFunctional
{
    public function testIssue1284(): void
    {
        $ideographicSpace = "\u{3000}";
        $nbsp = "\u{a0}";
        $spreadsheetOld = new Spreadsheet();
        $osheet = $spreadsheetOld->getActiveSheet();
        $osheet->getCell('A1')->setValue('# item 1');
        $osheet->getCell('A2')->setValue("$ideographicSpace# item 2");
        $osheet->getCell('A3')->setValue("$ideographicSpace$ideographicSpace# item 3");
        $osheet->getCell('A4')->setValue("$nbsp#  item\t4");
        $osheet->getCell('A5')->setValue("$nbsp$nbsp# item    5");

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Html');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('# item 1', $sheet->getCell('A1')->getValue(), 'nothing changed');
        self::assertSame("$ideographicSpace# item 2", $sheet->getCell('A2')->getValue(), 'nothing changed including 1 ideographic space');
        self::assertSame("$ideographicSpace$ideographicSpace# item 3", $sheet->getCell('A3')->getValue(), 'nothing changed including 2 ideographic spaces');
        self::assertSame("$nbsp# item 4", $sheet->getCell('A4')->getValue(), 'nbsp unchanged, 2 spaces reduced to 1, tab changed to space');
        self::assertSame("$nbsp$nbsp# item 5", $sheet->getCell('A5')->getValue(), 'many spaces reduced to 1');

        $spreadsheet->disconnectWorksheets();
    }
}
