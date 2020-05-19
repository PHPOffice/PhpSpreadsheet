<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheetTests\Functional;

class AllOrOneSheetTest extends Functional\AbstractFunctional
{
    public function testWriteAllSheets(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setCellValue('A1', 'first');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('A1', 'second');

        $writer = new Html($spreadsheet);
        self::assertFalse($writer->getEmbedImages());
        $writer->writeAllSheets();
        self::assertTrue($writer->getGenerateSheetNavigationBlock());
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(1, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(2, $divs);
        self::assertEquals('page: page0', $divs->item(0)->getAttribute('style'));
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('sheet0', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tbl->item(0)->getAttribute('class'));
        $tbl = $divs->item(1)->getElementsByTagName('table');
        self::assertEquals('page: page1', $divs->item(1)->getAttribute('style'));
        self::assertEquals('sheet1', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tbl->item(0)->getAttribute('class'));
        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testWriteAllSheetsNoNav(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setCellValue('A1', 'first');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('A1', 'second');

        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();
        $writer->setGenerateSheetNavigationBlock(false);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(2, $divs);
        self::assertEquals('page: page0', $divs->item(0)->getAttribute('style'));
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('sheet0', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tbl->item(0)->getAttribute('class'));
        $tbl = $divs->item(1)->getElementsByTagName('table');
        self::assertEquals('page: page1', $divs->item(1)->getAttribute('style'));
        self::assertEquals('sheet1', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tbl->item(0)->getAttribute('class'));
        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testWriteAllSheetsPdf(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setCellValue('A1', 'first');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('A1', 'second');

        $writer = new Mpdf($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(2, $divs);
        self::assertEquals('page: page0', $divs->item(0)->getAttribute('style'));
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('sheet0', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tbl->item(0)->getAttribute('class'));
        $tbl = $divs->item(1)->getElementsByTagName('table');
        self::assertEquals('page: page1', $divs->item(1)->getAttribute('style'));
        self::assertEquals('sheet1', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tbl->item(0)->getAttribute('class'));
    }

    public function testWriteOneSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setCellValue('A1', 'first');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('A1', 'second');

        $writer = new Html($spreadsheet);
        $writer->setSheetIndex(1);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(1, $divs);
        self::assertEquals('page: page1', $divs->item(0)->getAttribute('style'));
        $tbl = $divs->item(0)->getElementsByTagName('table');
        self::assertEquals('sheet1', $tbl->item(0)->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tbl->item(0)->getAttribute('class'));
        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testPageBreak(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridlines(true)->setPrintGridlines(true);
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 'before page break');
        $sheet->setBreak('A2', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        $sheet->setCellValue('A3', 'after page break');
        $sheet->setCellValue('A4', 4);
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', 'new sheet');

        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();

        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        self::assertCount(3, $divs);

        $sty = $divs[0]->getAttribute('style');
        $cls = $divs[0]->getAttribute('class');
        self::assertEquals('page: page0', $sty);
        self::assertEquals('', $cls);
        $sty = $divs[1]->getAttribute('style');
        $cls = $divs[1]->getAttribute('class');
        self::assertEquals('page: page0', $sty);
        self::assertEquals('scrpgbrk', $cls);
        $sty = $divs[2]->getAttribute('style');
        $cls = $divs[2]->getAttribute('class');
        self::assertEquals('page: page1', $sty);
        self::assertEquals('', $cls);

        $this->writeAndReload($spreadsheet, 'Html');
    }

    public function testTcpdfPageBreak(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setCellValue('A1', 'First sheet');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('A2', 'Second sheet');
        $sheet2->setCellValue('A2', 'before page break');
        $sheet2->setBreak('A2', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        $sheet2->setCellValue('A3', 'after page break');

        $writer = new Tcpdf($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHtmlAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')[0];
        $divs = $body->getElementsByTagName('div');
        self::assertCount(5, $divs);

        self::assertEquals('page: page0', $divs[0]->getAttribute('style'));
        self::assertEquals('page: page1', $divs[2]->getAttribute('style'));
        self::assertEquals('page: page1', $divs[4]->getAttribute('style'));
        self::assertEquals('page-break-before:always', $divs[1]->getAttribute('style'));
        self::assertEquals('page-break-before:always', $divs[3]->getAttribute('style'));
    }
}
