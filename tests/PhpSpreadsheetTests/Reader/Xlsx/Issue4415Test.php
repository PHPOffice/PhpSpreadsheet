<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4415Test extends TestCase
{
    private static string $file = 'tests/data/Reader/XLSX/issue.4415.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$file;
        $file .= '#xl/drawings/drawing1.xml';
        $data = file_get_contents($file) ?: '';
        $expected = '<a:alpha val="72600"/>';
        self::assertStringContainsString($expected, $data);
        $expected = '<a:alpha val="90100"/>';
        self::assertStringContainsString($expected, $data);
        self::assertSame(2, substr_count($data, '<a:alpha '), 'number of drawings with alpha');
        self::assertSame(2, substr_count($data, '<xdr:oneCellAnchor>'), 'first 2 drawings');
        self::assertSame(1, substr_count($data, '<xdr:twoCellAnchor>'), 'third drawings');
    }

    public function testFractionalAlpha(): void
    {
        $file = self::$file;
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(3, $drawings);
        self::assertNotNull($drawings[0]);
        self::assertNotNull($drawings[1]);
        self::assertNotNull($drawings[2]);
        self::assertSame(50, $drawings[0]->getShadow()->getAlpha());
        self::assertSame(72, $drawings[1]->getShadow()->getAlpha());
        self::assertSame('', $drawings[1]->getCoordinates2(), 'one cell anchor');
        self::assertSame(90, $drawings[2]->getShadow()->getAlpha());
        self::assertNotEquals('', $drawings[2]->getCoordinates2(), 'two cell anchor');
        $spreadsheet->disconnectWorksheets();
    }
}
