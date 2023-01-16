<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as Writer;
use PHPUnit\Framework\TestCase;

class FixHeightTest extends TestCase
{
    public function testFixHeight(): void
    {
        $spreadsheet = new Spreadsheet();
        //$sheet0 = $spreadsheet->getActiveSheet();
        $sheet1 = $spreadsheet->createSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $sheet1->getPageSetup()->setFitToHeight(1);
        $sheet2->getPageSetup()->setFitToHeight(2);
        $sheet3->getPageSetup()->setFitToPage(true);

        $writer = new Writer($spreadsheet);
        $writer->writeAllSheets();
        $header = $writer->generateHTMLHeader(true);
        self::assertStringContainsString('table.sheet0', $header);
        self::assertStringContainsString('table.sheet1', $header);
        self::assertStringContainsString('table.sheet2', $header);
        self::assertStringContainsString('table.sheet3', $header);
        $count = substr_count($header, 'break-inside');
        self::assertSame(4, $count); // 2 for sheet1, 2 for sheet3
        self::assertStringContainsString('table.sheet1 { page-break-inside:avoid; break-inside:avoid }', $header);
        self::assertStringContainsString('table.sheet3 { page-break-inside:avoid; break-inside:avoid }', $header);
        $spreadsheet->disconnectWorkSheets();
    }
}
