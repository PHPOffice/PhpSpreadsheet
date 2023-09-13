<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

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

        $image = file_get_contents(__DIR__ . '/../../../data/Reader/HTML/memoryDrawingTest.jpg');
        self::assertNotFalse($image, 'unable to read file');
        $image = imagecreatefromstring($image);
        self::assertNotFalse($image, 'unable to create image from string');
        $drawing = new MemoryDrawing();
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

    public static function dataProvider(): array
    {
        return [
            [33, 19, 0, 20],
            [129, 110, 12, -3],
            [55, 110, 33, 42],
        ];
    }
}
