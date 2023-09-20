<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class DrawingImageHyperlinkTest extends AbstractFunctional
{
    public function testDrawingImageHyperlinkTest(): void
    {
        $gdImage = @imagecreatetruecolor(120, 20);
        $textColor = ($gdImage === false) ? false : imagecolorallocate($gdImage, 255, 255, 255);
        if ($gdImage === false || $textColor === false) {
            self::fail('imagecreatetruecolor or imagecolorallocate failed');
        } else {
            $baseUrl = 'https://github.com/PHPOffice/PhpSpreadsheet';
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
            $hyperLink = new Hyperlink($baseUrl, 'test image');
            $drawing->setHyperlink($hyperLink);
            $drawing->setWorksheet($aSheet);

            $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
            $spreadsheet->disconnectWorksheets();

            foreach ($reloadedSpreadsheet->getActiveSheet()->getDrawingCollection() as $pDrawing) {
                $getHyperlink = $pDrawing->getHyperlink();
                if ($getHyperlink === null) {
                    self::fail('getHyperlink returned null');
                } else {
                    self::assertEquals('https://github.com/PHPOffice/PhpSpreadsheet', $getHyperlink->getUrl(), 'functional test drawing hyperlink');
                }
            }
            $reloadedSpreadsheet->disconnectWorksheets();
        }
    }
}
