<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingTest extends AbstractFunctional
{
    public function testDrawing(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters B, M, and P');
        $drawing->setDescription('Handwritten B, M, and P');
        $drawing->setPath('samples/images/bmp.bmp');
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('A1');

        $drawing = new Drawing();
        $drawing->setName('Letters G, I, and F');
        $drawing->setDescription('Handwritten G, I, and F');
        $drawing->setPath('samples/images/gif.gif');
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('E5');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $worksheet->getDrawingCollection();
        self::assertCount(2, $drawings);

        $drawing = $drawings[0] ?? null;
        self::assertInstanceOf(Drawing::class, $drawing);
        // original width is 123, some loss of precision due to rounding
        self::assertGreaterThanOrEqual(120, $drawing->getWidth());
        self::assertLessThanOrEqual(126, $drawing->getWidth());
        // original height is 81
        self::assertGreaterThanOrEqual(78, $drawing->getHeight());
        self::assertLessThanOrEqual(84, $drawing->getHeight());
        self::assertSame('Letters B, M, and P', $drawing->getName());
        self::assertSame('A1', $drawing->getCoordinates());

        $drawing = $drawings[1] ?? null;
        self::assertInstanceOf(Drawing::class, $drawing);
        // original width is 163, some loss of precision due to rounding
        self::assertGreaterThanOrEqual(160, $drawing->getWidth());
        self::assertLessThanOrEqual(166, $drawing->getWidth());
        // original height is 121
        self::assertGreaterThanOrEqual(118, $drawing->getHeight());
        self::assertLessThanOrEqual(124, $drawing->getHeight());
        self::assertSame('Letters G, I, and F', $drawing->getName());
        self::assertSame('E5', $drawing->getCoordinates());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
