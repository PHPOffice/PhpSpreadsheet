<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class ImageEmbedTest extends TestCase
{
    public function testImageEmbed(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $drawing = new Drawing();
        $drawing->setName('Not an image');
        $drawing->setDescription('Non-image');
        $drawing->setPath(__FILE__, false);
        $drawing->setCoordinates('A1');
        $drawing->setCoordinates2('E4');
        $drawing->setWorksheet($sheet);

        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        $drawing->setCoordinates('A5');
        $drawing->setCoordinates2('E8');
        $drawing->setWorksheet($sheet);

        $writer = new HtmlWriter($spreadsheet);
        $writer->setEmbedImages(true);
        $html = $writer->generateHTMLAll();
        self::assertSame(1, substr_count($html, '<img'));
        self::assertSame(1, substr_count($html, 'src="data'));
        self::assertSame(1, substr_count($html, 'src="data:image/png;base64,'));
        self::assertSame(0, substr_count($html, 'blue_square.png'));
        //self::assertSame(1, substr_count($html, 'src="data:," alt="Non-image"'));

        $spreadsheet->disconnectWorksheets();
    }
}
