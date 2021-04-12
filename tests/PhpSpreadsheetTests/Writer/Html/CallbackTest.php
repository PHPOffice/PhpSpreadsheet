<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CallbackTest extends Functional\AbstractFunctional
{
    public function yellowBody(string $html): string
    {
        $newstyle = <<<EOF
<style type='text/css'>
body {
    background-color: yellow;
}
</style>

EOF;

        return preg_replace('~</head>~', "$newstyle</head>", $html);
    }

    public function testSetAndReset(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1');

        $writer = new Html($spreadsheet);
        $html1 = $writer->generateHTMLall();
        $writer->setEditHtmlCallback([$this, 'yellowBody']);
        $html2 = $writer->generateHTMLall();
        $writer->setEditHtmlCallback(null);
        $html3 = $writer->generateHTMLall();

        self::assertFalse(strpos($html1, 'background-color: yellow'));
        self::assertNotFalse(strpos($html2, 'background-color: yellow'));
        self::assertFalse(strpos($html3, 'background-color: yellow'));
        self::assertEquals($html3, $html1);

        $writer->setEditHtmlCallback([$this, 'yellowBody']);
        $oufil = File::temporaryFilename();
        $writer->save($oufil);
        $html4 = file_get_contents($oufil);
        unlink($oufil);
        self::assertNotFalse(strpos($html4, 'background-color: yellow'));

        $this->writeAndReload($spreadsheet, 'Html');
    }
}
