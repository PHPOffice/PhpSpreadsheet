<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    public function testCloningWorksheetWithImages(): void
    {
        $gdImage = @imagecreatetruecolor(120, 20);
        $textColor = ($gdImage === false) ? false : imagecolorallocate($gdImage, 255, 255, 255);
        if ($gdImage === false || $textColor === false) {
            self::fail('imagecreatetruecolor or imagecolorallocate failed');
        } else {
            $spreadsheet = new Spreadsheet();
            $aSheet = $spreadsheet->getActiveSheet();
            imagestring($gdImage, 1, 5, 5, 'Created with PhpSpreadsheet', $textColor);

            $drawing = new MemoryDrawing();
            $drawing->setName('In-Memory image 1');
            $drawing->setDescription('In-Memory image 1');
            $drawing->setCoordinates('A1');
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(
                MemoryDrawing::RENDERING_JPEG
            );
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setHeight(36);
            $drawing->setWorksheet($aSheet);

            $originDrawingCount = count($aSheet->getDrawingCollection());
            $clonedWorksheet = clone $aSheet;
            $clonedDrawingCount = count($clonedWorksheet->getDrawingCollection());

            self::assertEquals($originDrawingCount, $clonedDrawingCount);
            self::assertNotSame($aSheet, $clonedWorksheet);
            self::assertNotSame($aSheet->getDrawingCollection(), $clonedWorksheet->getDrawingCollection());
            $spreadsheet->disconnectWorksheets();
        }
    }
}
