<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReaderLoadCommentsTest extends AbstractFunctional
{
    /**
     * Test load Xlsx file with comment in sheet
     * to load proper count of comments in correct coords.
     */
    public function testXlsxLoadWithComment()
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getCell('E10')->setValue('Comment');
        $spreadsheet->getActiveSheet()
            ->getComment('E10')
            ->getText()
            ->createText('Comment to test');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        $commentsLoaded = $reloadedSpreadsheet->getSheet(0)->getComments();
        self::assertCount(1, $commentsLoaded);

        $commentCoordinate = key($commentsLoaded);
        self::assertSame('E10', $commentCoordinate);
    }
}
