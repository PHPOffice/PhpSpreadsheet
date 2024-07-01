<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class MultiLineCommentTest extends AbstractFunctional
{
    public function testMultipleParagraphs(): void
    {
        $filename = 'tests/data/Reader/Ods/issue.4081.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame("First line.\n\nSecond line.", $sheet->getComment('A1')->getText()->getPlainText());
        $spreadsheet->disconnectWorksheets();
    }

    public function testOneParagraphMultipleSpans(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheetOld = $spreadsheetOld->getActiveSheet();
        $sheetOld->getCell('A1')->setValue('Hello');
        $text = $sheetOld->getComment('A1')->getText();
        $text->createText('First');
        $text->createText(' line.');
        $text->createText("\n");
        $text->createText("\n");
        $text->createText("Second line.\nThird line.");
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame("First line.\n\nSecond line.\nThird line.", $sheet->getComment('A1')->getText()->getPlainText());
        $spreadsheet->disconnectWorksheets();
    }
}
