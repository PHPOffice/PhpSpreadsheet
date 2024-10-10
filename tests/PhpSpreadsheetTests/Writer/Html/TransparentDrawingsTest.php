<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class TransparentDrawingsTest extends TestCase
{
    public function testHtmlTransparentDrawing(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridLines(false);

        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertEquals($drawing->getWidth(), 100);
        self::assertEquals($drawing->getHeight(), 100);
        $drawing->setCoordinates('A1');
        $drawing->setCoordinates2('E8');
        $drawing->setOpacity(25000);
        $drawing->setWorksheet($sheet);

        $writer = new HtmlWriter($spreadsheet);
        $content = $writer->generateHTMLAll();
        self::assertStringContainsString('opacity:0.25;', $content);
        $spreadsheet->disconnectWorksheets();
    }

    public function testHtmlTransparentMemoryDrawing(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridLines(false);

        $contents = file_get_contents('tests/data/Writer/XLSX/blue_square.png');
        $drawing = MemoryDrawing::fromString("$contents");
        $drawing->setName('Blue Square');
        self::assertEquals($drawing->getWidth(), 100);
        self::assertEquals($drawing->getHeight(), 100);
        $drawing->setCoordinates('A1');
        $drawing->setCoordinates2('E8');
        $drawing->setOpacity(25000);
        $drawing->setWorksheet($sheet);

        $writer = new HtmlWriter($spreadsheet);
        $content = $writer->generateHTMLAll();
        self::assertStringContainsString('opacity:0.25;', $content);
        $spreadsheet->disconnectWorksheets();
    }
}
