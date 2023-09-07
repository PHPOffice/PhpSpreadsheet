<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class RepeatedRowsTest extends Functional\AbstractFunctional
{
    public function testWriteRepeats(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setRowsToRepeatAtTop([1, 2]);
        $sheet->setCellValue('A1', 'Repeat1');
        $sheet->setCellValue('A2', 'Repeat2');
        for ($row = 3; $row <= 100; ++$row) {
            $sheet->setCellValue("A$row", $row);
        }

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLall();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('', $tbl->item(0)->getAttribute('style'));
        $thd = $divs->item(0)->getElementsByTagName('thead');
        self::assertCount(1, $thd);
        $trw = $thd->item(0)->getElementsByTagName('tr');
        self::assertCount(2, $trw);
        $tbd = $divs->item(0)->getElementsByTagName('tbody');
        self::assertCount(1, $tbd);
        $trw = $tbd->item(0)->getElementsByTagName('tr');
        self::assertCount(98, $trw);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testWriteNoRepeats(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //$sheet->getPageSetup()->setRowsToRepeatAtTop([1, 2]);
        $sheet->setCellValue('A1', 'Repeat1');
        $sheet->setCellValue('A2', 'Repeat2');
        for ($row = 3; $row <= 100; ++$row) {
            $sheet->setCellValue("A$row", $row);
        }

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLall();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        $tbl = $divs->item(0)->getElementsByTagName('table');
        $thd = $tbl->item(0)->getElementsByTagName('thead');
        self::assertCount(0, $thd);
        //$trw = $thd->item(0)->getElementsByTagName('tr');
        //self::assertCount(2, $trw);
        $tbd = $divs->item(0)->getElementsByTagName('tbody');
        self::assertCount(1, $tbd);
        $trw = $tbd->item(0)->getElementsByTagName('tr');
        self::assertCount(100, $trw);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testWriteRepeatsInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setRowsToRepeatAtTop([1, 2]);
        $sheet->setCellValue('A1', 'Repeat1');
        $sheet->setCellValue('A2', 'Repeat2');
        for ($row = 3; $row <= 100; ++$row) {
            $sheet->setCellValue("A$row", $row);
        }

        $writer = new Html($spreadsheet);
        self::assertFalse($writer->getUseInlineCss());
        $writer->setUseInlineCss(true);
        $html = $writer->generateHTMLall();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('border-collapse:collapse', $tbl->item(0)->getAttribute('style'));
        $thd = $divs->item(0)->getElementsByTagName('thead');
        self::assertCount(1, $thd);
        $trw = $thd->item(0)->getElementsByTagName('tr');
        self::assertCount(2, $trw);
        $tbd = $divs->item(0)->getElementsByTagName('tbody');
        self::assertCount(1, $tbd);
        $trw = $tbd->item(0)->getElementsByTagName('tr');
        self::assertCount(98, $trw);

        $this->writeAndReload($spreadsheet, 'Html');
    }
}
