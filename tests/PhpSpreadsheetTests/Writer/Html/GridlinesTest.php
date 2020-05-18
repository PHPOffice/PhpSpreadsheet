<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class GridlinesTest extends Functional\AbstractFunctional
{
    public function testGridlines(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridlines(true)->setPrintGridlines(true);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(true)->setPrintGridlines(false);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(false)->setPrintGridlines(true);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(false)->setPrintGridlines(false);
        $sheet->setCellValue('A1', 1);

        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();

        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        self::assertCount(4, $divs);

        $tbl = $divs[0]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('sheet0 gridlines gridlinesp', $cls);
        $tbl = $divs[1]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('sheet1 gridlines', $cls);
        $tbl = $divs[2]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('sheet2 gridlinesp', $cls);
        $tbl = $divs[3]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('sheet3', $cls);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testGridlinesInline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridlines(true)->setPrintGridlines(true);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(true)->setPrintGridlines(false);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(false)->setPrintGridlines(true);
        $sheet->setCellValue('A1', 1);
        $sheet = $spreadsheet->createSheet();
        $sheet->setShowGridlines(false)->setPrintGridlines(false);
        $sheet->setCellValue('A1', 1);

        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();
        $writer->setUseInlineCss(true);

        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        self::assertCount(4, $divs);

        $tbl = $divs[0]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('gridlines gridlinesp', $cls);
        $tbl = $divs[1]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('gridlines', $cls);
        $tbl = $divs[2]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('gridlinesp', $cls);
        $tbl = $divs[3]->getElementsByTagName('table')[0];
        $cls = $tbl->getAttribute('class');
        self::assertEquals('', $cls);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testRichText(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $emc2 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $part1 = $emc2->createTextRun('e=mc');
        $part1->getFont()->getColor()->setARGB(Color::COLOR_BLUE);
        $part2 = $emc2->createTextRun('2');
        $font = $part2->getFont();
        $font->getColor()->setARGB(Color::COLOR_DARKGREEN);
        $font->setSuperScript(true);
        $sheet->setCellValue('A1', $emc2);
        $h2o = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $h2o->createTextRun('H');
        $part2 = $h2o->createTextRun('2');
        $font = $part2->getFont();
        $font->setSubScript(true);
        $font->getColor()->setARGB(Color::COLOR_RED);
        $h2o->createTextRun('O');
        $sheet->setCellValue('A2', $h2o);
        $h2so4 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $h2so4->createTextRun('H');
        $part2 = $h2so4->createTextRun('2');
        $font = $part2->getFont();
        $font->setSubScript(true);
        $h2so4->createTextRun('SO');
        $part4 = $h2so4->createTextRun('4');
        $part4->getFont()->setSubScript(true);
        $sheet->setCellValue('A3', $h2so4);
        $sheet->setCellValue('A4', '5');
        $sheet->getCell('A4')->getStyle()->getFont()->setSuperScript(true);
        $sheet->setCellValue('A5', '6');
        $sheet->getCell('A5')->getStyle()->getFont()->setSubScript(true);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        self::assertCount(1, $divs);

        $tabl = $divs[0]->getElementsByTagName('table');
        $tbod = $tabl[0]->getElementsByTagName('tbody');
        $rows = $tbod[0]->getElementsByTagName('tr');
        self::assertCount(5, $rows);
        $tds = $rows[0]->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds[0]->getElementsByTagName('span');
        self::assertCount(2, $spans);
        self::assertEquals('e=mc', $spans[0]->textContent);
        $style = $spans[0]->getAttribute('style');
        self::assertEquals(1, preg_match('/color:#0000FF/', $style));
        $style = $spans[1]->getAttribute('style');
        self::assertEquals(1, preg_match('/color:#008000/', $style));
        $sups = $spans[1]->getElementsByTagName('sup');
        self::assertCount(1, $sups);
        assert('2' == $sups[0]->textContent);

        $tds = $rows[1]->getElementsByTagName('td');
        assert(1 == count($tds));
        $spans = $tds[0]->getElementsByTagName('span');
        assert(3 == count($spans));
        assert('H' == $spans[0]->textContent);
        $style = $spans[1]->getAttribute('style');
        assert(1 == preg_match('/color:#FF0000/', $style));
        $subs = $spans[1]->getElementsByTagName('sub');
        assert(1 == count($subs));
        assert('2' == $subs[0]->textContent);
        assert('O' == $spans[2]->textContent);

        $tds = $rows[2]->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds[0]->getElementsByTagName('span');
        self::assertCount(4, $spans);
        self::assertEquals('H', $spans[0]->textContent);
        $subs = $spans[1]->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('2', $subs[0]->textContent);
        self::assertEquals('SO', $spans[2]->textContent);
        $subs = $spans[3]->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('4', $subs[0]->textContent);

        $tds = $rows[3]->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds[0]->getElementsByTagName('span');
        self::assertCount(0, $spans);
        $sups = $tds[0]->getElementsByTagName('sup');
        self::assertCount(1, $sups);
        self::assertEquals('5', $sups[0]->textContent);

        $tds = $rows[4]->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds[0]->getElementsByTagName('span');
        self::assertCount(0, $spans);
        $subs = $tds[0]->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('6', $subs[0]->textContent);

        $this->writeAndReload($spreadsheet, 'Html');
    }
}
