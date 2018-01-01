<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HtmlCommentsTest extends AbstractFunctional
{
    private $value = 'I am comment.';
    private $spreadsheet;

    private function createComment()
    {
        $this->spreadsheet = new Spreadsheet();

        $this->spreadsheet->getActiveSheet()->getCell('A1')->setValue('Comment');

        return $this->spreadsheet->getActiveSheet()
            ->getComment('A1')
            ->getText()->createTextRun($this->value);
    }

    private function doTheTest($string)
    {
        $reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Html');

        $actual = (string) $reloadedSpreadsheet->getActiveSheet()->getComment('A1')->getText()->getPlainText();
        self::assertSame($this->value, $actual, $string);
    }

    public function testPlainTextComment()
    {
        $this->createComment();
        $this->doTheTest('should be able to read and write plain text comments from and to html as plain text');
    }

    public function testRichTextComment()
    {
        $this->createComment()->getFont()->setBold(true);
        $this->doTheTest('should be able to read and write rich text comments from and to html as plain text');
    }
}
