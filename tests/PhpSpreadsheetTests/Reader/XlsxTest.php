<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }

    /**
     * Test load Xlsx file with comment in sheet.
     */
    public function testLoadXlsxWithComment()
    {
        $filename = './data/Reader/XLSX/with_comment.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $commentsLoaded = $spreadsheet->getSheet(0)->getComments();
        self::assertCount(1, $commentsLoaded);

        $commentCoordinate = key($commentsLoaded);
        self::assertSame('E10', $commentCoordinate);
    }
}
