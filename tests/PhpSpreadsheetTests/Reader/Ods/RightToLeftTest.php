<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RightToLeftTest extends AbstractFunctional
{
    public function testReadRightToLeft(): void
    {
        // Written by LibreOffice: style:writing-mode="rl-tb" in the table style of sheet RTL
        $spreadsheet = (new OdsReader())->load('tests/data/Reader/Ods/RightToLeft.ods');
        self::assertTrue($spreadsheet->getSheetByNameOrThrow('RTL')->getRightToLeft());
        self::assertFalse($spreadsheet->getSheetByNameOrThrow('LTR')->getRightToLeft());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteRightToLeft(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $spreadsheetOld->getActiveSheet()->setTitle('RTL')->setRightToLeft(true);
        $spreadsheetOld->createSheet()->setTitle('LTR');

        $content = (new OdsWriter($spreadsheetOld))->getWriterPartContent()->write();
        self::assertSame(1, substr_count($content, 'style:writing-mode="rl-tb"'));

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();
        self::assertTrue($spreadsheet->getSheetByNameOrThrow('RTL')->getRightToLeft());
        self::assertFalse($spreadsheet->getSheetByNameOrThrow('LTR')->getRightToLeft());
        $spreadsheet->disconnectWorksheets();
    }
}
