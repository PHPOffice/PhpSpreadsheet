<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class MemoryDrawingOffsetTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */ 
    public function testMemoryDrawingOffset($w ,$h ,$x ,$y): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $drawing = new MemoryDrawing();
        $drawing->setImageResource(\imagecreatefromstring(file_get_contents("https://avatars.githubusercontent.com/u/1836015"))) //->setPath($this->getPath())//  
            ->setResizeProportional(false) //是否保持比例
            ->setWidthAndHeight($w, $h) //图片宽高,原始尺寸 100*100
            ->setOffsetX($x)
            ->setOffsetY($y)
            ->setWorksheet($sheet);
        
        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('width:' . $w . 'px;left: ' .$x . 'px; top: ' . $y . 'px;position: absolute;', $html);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    public function dataProvider()
    {
        return [
            [33, 19, 0 ,20],
            [129, 110, 12,-3],
            [55, 110, 33,42],
        ];
    }
}
