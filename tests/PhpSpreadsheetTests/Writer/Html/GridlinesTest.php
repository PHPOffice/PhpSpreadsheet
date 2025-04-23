<?php

declare(strict_types=1);

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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(4, $divs);

        $tbl = $divs->item(0)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertSame('sheet0 gridlines gridlinesp', $cls);
        $tbl = $divs->item(1)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertSame('sheet1 gridlines', $cls);
        $tbl = $divs->item(2)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertSame('sheet2 gridlinesp', $cls);
        $tbl = $divs->item(3)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertSame('sheet3', $cls);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(4, $divs);

        $tbl = $divs->item(0)?->getElementsByTagName('table')->item(0);
        self::assertNotNull($tbl);
        $cls = $tbl->getAttribute('class');
        self::assertSame('gridlines gridlinesp', $cls);
        $tbl = $divs->item(1)?->getElementsByTagName('table')->item(0);
        self::assertNotNull($tbl);
        $cls = $tbl->getAttribute('class');
        self::assertSame('gridlines', $cls);
        $tbl = $divs->item(2)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertEquals('gridlinesp', $cls);
        $tbl = $divs->item(3)?->getElementsByTagName('table')->item(0);
        $cls = $tbl?->getAttribute('class');
        self::assertSame('', $cls);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }

    public function testRichText(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $emc2 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $part1 = $emc2->createTextRun('e=mc');
        $font = $part1->getFont();
        self::assertNotNull($font);
        $font->getColor()->setARGB(Color::COLOR_BLUE);
        $part2 = $emc2->createTextRun('2');
        $font = $part2->getFont();
        self::assertNotNull($font);
        $font->getColor()->setARGB(Color::COLOR_DARKGREEN);
        $font->setSuperScript(true);
        $sheet->setCellValue('A1', $emc2);
        $h2o = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $h2o->createTextRun('H');
        $part2 = $h2o->createTextRun('2');
        $font = $part2->getFont();
        self::assertNotNull($font);
        $font->setSubScript(true);
        $font->getColor()->setARGB(Color::COLOR_RED);
        $h2o->createTextRun('O');
        $sheet->setCellValue('A2', $h2o);
        $h2so4 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $h2so4->createTextRun('H');
        $part2 = $h2so4->createTextRun('2');
        $font = $part2->getFont();
        self::assertNotNull($font);
        $font->setSubScript(true);
        $h2so4->createTextRun('SO');
        $part4 = $h2so4->createTextRun('4');
        $font = $part4->getFont();
        self::assertNotNull($font);
        $font->setSubScript(true);
        $sheet->setCellValue('A3', $h2so4);
        $sheet->setCellValue('A4', '5');
        $sheet->getCell('A4')->getStyle()->getFont()->setSuperScript(true);
        $sheet->setCellValue('A5', '6');
        $sheet->getCell('A5')->getStyle()->getFont()->setSubScript(true);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(1, $divs);

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        self::assertNotNull($tabl);
        $tbod = $tabl->item(0)?->getElementsByTagName('tbody');
        self::assertNotNull($tbod);
        $rows = $tbod->item(0)?->getElementsByTagName('tr');
        self::assertNotNull($rows);
        self::assertCount(5, $rows);
        $tds = $rows->item(0)?->getElementsByTagName('td');
        self::assertNotNull($tds);
        self::assertCount(1, $tds);
        $spans = $tds->item(0)?->getElementsByTagName('span');
        self::assertNotNull($spans);
        self::assertCount(2, $spans);
        self::assertEquals('e=mc', $spans->item(0)?->textContent);
        $style = $spans->item(0)?->getAttribute('style');
        self::assertEquals(1, preg_match('/color:#0000FF/', "$style"));
        $style = $spans->item(1)?->getAttribute('style');
        self::assertEquals(1, preg_match('/color:#008000/', "$style"));
        $sups = $spans->item(1)?->getElementsByTagName('sup');
        self::assertCount(1, $sups);
        self::assertSame('2', $sups?->item(0)?->textContent);

        $tds = $rows->item(1)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(3, $spans);
        self::assertSame('H', $spans?->item(0)?->textContent);
        $style = $spans->item(1)?->getAttribute('style');
        self::assertSame(1, preg_match('/color:#FF0000/', "$style"));
        $subs = $spans->item(1)?->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertSame('2', $subs?->item(0)?->textContent);
        self::assertSame('O', $spans->item(2)?->textContent);

        $tds = $rows->item(2)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(4, $spans);
        self::assertEquals('H', $spans?->item(0)?->textContent);
        $subs = $spans?->item(1)?->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('2', $subs?->item(0)?->textContent);
        self::assertEquals('SO', $spans?->item(2)?->textContent);
        $subs = $spans?->item(3)?->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('4', $subs?->item(0)?->textContent);

        $tds = $rows->item(3)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        $sups = $tds?->item(0)?->getElementsByTagName('sup');
        self::assertCount(1, $sups);
        self::assertEquals('5', $sups?->item(0)?->textContent);

        $tds = $rows->item(4)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $spans = $tds?->item(0)?->getElementsByTagName('span');
        self::assertCount(0, $spans);
        $subs = $tds?->item(0)?->getElementsByTagName('sub');
        self::assertCount(1, $subs);
        self::assertEquals('6', $subs?->item(0)?->textContent);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
    }
}
