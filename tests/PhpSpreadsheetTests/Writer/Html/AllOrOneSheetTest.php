<?php

declare(strict_types=1);

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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(1, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(2, $divs);
        $divsItem0 = $divs->item(0);
        self::assertNotNull($divsItem0);
        $divsItem1 = $divs->item(1);
        self::assertNotNull($divsItem1);
        self::assertEquals('page: page0', $divsItem0->getAttribute('style'));
        $tbl = $divsItem0->getElementsByTagName('table');
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet0', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tblItem0->getAttribute('class'));
        $tbl = $divsItem1->getElementsByTagName('table');
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('page: page1', $divsItem1->getAttribute('style'));
        self::assertEquals('sheet1', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tblItem0->getAttribute('class'));
        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(2, $divs);
        $divsItem0 = $divs->item(0);
        self::assertNotNull($divsItem0);
        self::assertEquals('page: page0', $divsItem0->getAttribute('style'));
        $tbl = $divsItem0->getElementsByTagName('table');
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet0', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tblItem0->getAttribute('class'));
        $divsItem1 = $divs->item(1);
        self::assertNotNull($divsItem1);
        $tbl = $divsItem1->getElementsByTagName('table');
        self::assertEquals('page: page1', $divsItem1->getAttribute('style'));
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet1', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tblItem0->getAttribute('class'));
        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        $divsItem0 = $divs->item(0);
        self::assertNotNull($divsItem0);
        self::assertCount(2, $divs);
        self::assertEquals('page: page0', $divsItem0->getAttribute('style'));
        $tbl = $divsItem0->getElementsByTagName('table');
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet0', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet0 gridlines', $tblItem0->getAttribute('class'));
        $divsItem1 = $divs->item(1);
        self::assertNotNull($divsItem1);
        $tbl = $divsItem1->getElementsByTagName('table');
        self::assertEquals('page: page1', $divsItem1->getAttribute('style'));
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet1', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tblItem0->getAttribute('class'));
        $spreadsheet->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('ul'); // sheet navigation
        self::assertCount(0, $divs);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(1, $divs);
        $divsItem0 = $divs->item(0);
        self::assertNotNull($divsItem0);
        self::assertEquals('page: page1', $divsItem0->getAttribute('style'));
        $tbl = $divsItem0->getElementsByTagName('table');
        $tblItem0 = $tbl->item(0);
        self::assertNotNull($tblItem0);
        self::assertEquals('sheet1', $tblItem0->getAttribute('id'));
        self::assertEquals('sheet1 gridlines', $tblItem0->getAttribute('class'));
        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(3, $divs);

        $divsItem0 = $divs->item(0);
        $divsItem1 = $divs->item(1);
        $divsItem2 = $divs->item(2);
        self::assertNotNull($divsItem0);
        self::assertNotNull($divsItem1);
        self::assertNotNull($divsItem2);
        $sty = $divsItem0->getAttribute('style');
        $cls = $divsItem0->getAttribute('class');
        self::assertEquals('page: page0', $sty);
        self::assertEquals('', $cls);
        $sty = $divsItem1->getAttribute('style');
        $cls = $divsItem1->getAttribute('class');
        self::assertEquals('page: page0', $sty);
        self::assertEquals('scrpgbrk', $cls);
        $sty = $divsItem2->getAttribute('style');
        $cls = $divsItem2->getAttribute('class');
        self::assertEquals('page: page1', $sty);
        self::assertEquals('', $cls);

        $rls = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();
        $rls->disconnectWorksheets();
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
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');
        self::assertCount(5, $divs);

        self::assertEquals('page: page0', $divs->item(0)?->getAttribute('style'));
        self::assertEquals('page: page1', $divs->item(2)?->getAttribute('style'));
        self::assertEquals('page: page1', $divs->item(4)?->getAttribute('style'));
        self::assertEquals('page-break-before:always', $divs->item(1)?->getAttribute('style'));
        self::assertEquals('page-break-before:always', $divs->item(3)?->getAttribute('style'));
        $spreadsheet->disconnectWorksheets();
    }
}
