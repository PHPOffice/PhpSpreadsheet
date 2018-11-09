<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CommentsTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Html'],
            ['Xlsx'],
            ['Ods'],
        ];
    }

    /**
     * Test load file with comment in sheet to load proper
     * count of comments in correct coords.
     *
     * @dataProvider providerFormats
     *
     * @param $format
     */
    public function testComments($format)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getCell('E10')->setValue('Comment');
        $spreadsheet->getActiveSheet()
            ->getComment('E10')
            ->getText()
            ->createText('Comment to test');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        $commentsLoaded = $reloadedSpreadsheet->getSheet(0)->getComments();
        self::assertCount(1, $commentsLoaded);

        $commentCoordinate = key($commentsLoaded);
        self::assertSame('E10', $commentCoordinate);
    }
}
