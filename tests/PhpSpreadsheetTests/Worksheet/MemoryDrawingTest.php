<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use GdImage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\TestCase;

class MemoryDrawingTest extends TestCase
{
    public function testMemoryDrawing(): void
    {
        $name = 'In-Memory image';
        $gdImage = @imagecreatetruecolor(120, 20);
        if ($gdImage === false) {
            self::markTestSkipped('Unable to create GD Image for MemoryDrawing');
        }

        $textColor = (int) imagecolorallocate($gdImage, 255, 255, 255);
        imagestring($gdImage, 1, 5, 5, 'Created with PhpSpreadsheet', $textColor);

        $drawing = new MemoryDrawing();
        $drawing->setName($name);
        $drawing->setDescription('In-Memory image 1');
        $drawing->setCoordinates('A1');
        $drawing->setImageResource($gdImage);
        $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
        $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);

        self::assertIsObject($drawing->getImageResource());
        self::assertInstanceOf(GdImage::class, $drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_DEFAULT, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_DEFAULT, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingFromString(): void
    {
        $imageFile = __DIR__ . '/../../data/Worksheet/officelogo.jpg';

        $imageString = file_get_contents($imageFile);
        if ($imageString === false) {
            self::markTestSkipped('Unable to read Image file for MemoryDrawing');
        }
        $drawing = MemoryDrawing::fromString($imageString);

        self::assertIsObject($drawing->getImageResource());
        self::assertInstanceOf(GdImage::class, $drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_JPEG, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_JPEG, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingFromInvalidString(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Value cannot be converted to an image');

        $imageString = 'I am not an image';
        MemoryDrawing::fromString($imageString);
    }

    public function testMemoryDrawingFromStream(): void
    {
        $imageFile = __DIR__ . '/../../data/Worksheet/officelogo.jpg';

        $imageStream = fopen($imageFile, 'rb');
        if ($imageStream === false) {
            self::markTestSkipped('Unable to read Image file for MemoryDrawing');
        }
        $drawing = MemoryDrawing::fromStream($imageStream);
        fclose($imageStream);

        self::assertIsObject($drawing->getImageResource());
        self::assertInstanceOf(GdImage::class, $drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_JPEG, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_JPEG, $drawing->getRenderingFunction());
    }
}
