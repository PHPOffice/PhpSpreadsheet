<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Mpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PHPUnit\Framework\TestCase;

class OrientationTest extends TestCase
{
    private const INITARRAY = [
        [1, 2, 3, 4, 5],
        [6, 7, 8, 9, 10],
        [11, 12, 13, 14, 15],
    ];

    private static function setupSheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->fromArray(self::INITARRAY);
        $sheet1->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->fromArray(self::INITARRAY);
        $sheet2->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->fromArray(self::INITARRAY);
        $sheet3->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        return $spreadsheet;
    }

    public static function testSheetOrientation(): void
    {
        $spreadsheet = self::setupSheet();
        $writer = new Mpdf($spreadsheet);
        //$writer->setOrientation( PageSetup::ORIENTATION_LANDSCAPE );
        $writer->writeAllSheets();
        $html = $writer->generateHtmlAll();
        self::assertSame(2, substr_count($html, 'size: landscape;'));
        self::assertSame(1, substr_count($html, 'size: portrait;'));
        $spreadsheet->disconnectWorksheets();
    }

    public static function testLandscape(): void
    {
        $spreadsheet = self::setupSheet();
        $writer = new Mpdf($spreadsheet);
        $writer->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $writer->writeAllSheets();
        $html = $writer->generateHtmlAll();
        self::assertSame(3, substr_count($html, 'size: landscape;'));
        self::assertSame(0, substr_count($html, 'size: portrait;'));
        $spreadsheet->disconnectWorksheets();
    }

    public static function testPortrait(): void
    {
        $spreadsheet = self::setupSheet();
        $writer = new Mpdf($spreadsheet);
        $writer->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $writer->writeAllSheets();
        $html = $writer->generateHtmlAll();
        self::assertSame(0, substr_count($html, 'size: landscape;'));
        self::assertSame(3, substr_count($html, 'size: portrait;'));
        $spreadsheet->disconnectWorksheets();
    }
}
