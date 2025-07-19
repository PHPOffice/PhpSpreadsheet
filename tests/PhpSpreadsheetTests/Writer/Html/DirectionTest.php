<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class DirectionTest extends TestCase
{
    public function testMixedRtlAndLtr(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setRightToLeft(true);
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setRightToLeft(true);
        $cells = [
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
        ];
        $sheet1->fromArray($cells);
        $sheet2->fromArray($cells);
        $sheet3->fromArray($cells);
        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHTMLall();
        $rtlCount = substr_count($html, "dir='rtl'");
        self::assertSame(2, $rtlCount);
        $ltrCount = substr_count($html, "dir='ltr'");
        self::assertSame(1, $ltrCount);
        $spreadsheet->disconnectWorksheets();
    }

    public function testNoRtl(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $cells = [
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
        ];
        $sheet1->fromArray($cells);
        $sheet2->fromArray($cells);
        $sheet3->fromArray($cells);
        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHTMLall();
        $rtlCount = substr_count($html, "dir='rtl'");
        self::assertSame(0, $rtlCount);
        $ltrCount = substr_count($html, "dir='ltr'");
        self::assertSame(0, $ltrCount);
        $spreadsheet->disconnectWorksheets();
    }

    public function testOnlyRtl(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        $cells = [
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
        ];
        $sheet1->fromArray($cells);
        $sheet1->setRightToLeft(true);
        $sheet2->fromArray($cells);
        $sheet2->setRightToLeft(true);
        $sheet3->fromArray($cells);
        $sheet3->setRightToLeft(true);
        $writer = new Html($spreadsheet);
        $writer->writeAllSheets();
        $html = $writer->generateHTMLall();
        $rtlCount = substr_count($html, "dir='rtl'");
        self::assertSame(4, $rtlCount, '3 sheets plus html tag');
        $ltrCount = substr_count($html, "dir='ltr'");
        self::assertSame(0, $ltrCount);
        $spreadsheet->disconnectWorksheets();
    }
}
