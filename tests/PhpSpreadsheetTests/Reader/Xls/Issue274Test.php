<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\TestCase;

/**
 * Xls file (issue.274.xlsx converted by LibreOffice) with a compressed EMF picture.
 */
class Issue274Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLS/issue.274.xls';

    public function testReadEmfPicture(): void
    {
        $spreadsheet = (new Xls())->load(self::$testbook);
        $drawings = $spreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];

        // The EMF picture is rendered as PNG
        self::assertInstanceOf(MemoryDrawing::class, $drawing);
        self::assertSame(MemoryDrawing::MIMETYPE_PNG, $drawing->getMimeType());
        $image = $drawing->getImageResource();
        self::assertNotNull($image);
        self::assertSame(131, imagesx($image));
        self::assertSame(131, imagesy($image));
        // The black background of the EMF picture is covered by the bitmap
        self::assertNotSame(0x000000, imagecolorat($image, 64, 64) & 0xFFFFFF);

        $spreadsheet->disconnectWorksheets();
    }
}
