<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PHPUnit\Framework\TestCase;

class MemoryDrawingOffsetTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testMemoryDrawingOffset(int $w, int $h, int $x, int $y): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $drawing = new MemoryDrawing();
        if (($image = file_get_contents('https://avatars.githubusercontent.com/u/1836015')) === false) {
            throw new Exception('image err1');
        }
        if (($image = imagecreatefromstring($image)) === false) {
            throw new Exception('image err2');
        }

        $drawing->setImageResource($image)
            ->setResizeProportional(false) //是否保持比例
            ->setWidthAndHeight($w, $h) //图片宽高,原始尺寸 100*100
            ->setOffsetX($x)
            ->setOffsetY($y)
            ->setWorksheet($sheet);

        $writer = new Html($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('width:' . $w . 'px;left: ' . $x . 'px; top: ' . $y . 'px;position: absolute;', $html);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    public function dataProvider(): array
    {
        return [
            [33, 19, 0, 20],
            [129, 110, 12, -3],
            [55, 110, 33, 42],
        ];
    }
}
