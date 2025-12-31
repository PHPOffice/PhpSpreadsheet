<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\TestCase;

class MemoryDrawingTest extends TestCase
{
    public function testMemoryDrawing(): void
    {
        $name = 'In-Memory image';
        $gdImage = @imagecreatetruecolor(120, 20);
        self::assertNotFalse($gdImage);

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

        self::assertSame(MemoryDrawing::MIMETYPE_DEFAULT, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_DEFAULT, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingFromString(): void
    {
        $imageFile = __DIR__ . '/../../data/Worksheet/officelogo.jpg';

        $imageString = file_get_contents($imageFile);
        self::assertNotFalse($imageString);
        $drawing = MemoryDrawing::fromString($imageString);

        self::assertIsObject($drawing->getImageResource());

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
        self::assertNotFalse($imageStream);
        $drawing = MemoryDrawing::fromStream($imageStream);
        fclose($imageStream);

        self::assertIsObject($drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_JPEG, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_JPEG, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingFromStreamNoGetImageSize(): void
    {
        $imageFile = __DIR__ . '/../../data/Worksheet/officelogo.jpg';

        $imageStream = fopen($imageFile, 'rb');
        self::assertNotFalse($imageStream);
        $drawing = MemoryDrawing2::fromStream($imageStream);
        fclose($imageStream);

        self::assertIsObject($drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_DEFAULT, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_DEFAULT, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingGif(): void
    {
        $imageFile = __DIR__ . '/../../data/Writer/XLSX/green_square.gif';

        $imageStream = fopen($imageFile, 'rb');
        self::assertNotFalse($imageStream);
        $drawing = MemoryDrawing::fromStream($imageStream);
        fclose($imageStream);

        self::assertIsObject($drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_GIF, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_GIF, $drawing->getRenderingFunction());
    }

    public function testMemoryDrawingBmp(): void
    {
        $imageFile = __DIR__ . '/../../data/Writer/XLSX/brown_square_256.bmp';

        $imageStream = fopen($imageFile, 'rb');
        self::assertNotFalse($imageStream);
        $drawing = MemoryDrawing::fromStream($imageStream);
        fclose($imageStream);

        self::assertIsObject($drawing->getImageResource());

        self::assertSame(MemoryDrawing::MIMETYPE_DEFAULT, $drawing->getMimeType(), 'bmp not supporteed - use default');
        self::assertSame(MemoryDrawing::RENDERING_DEFAULT, $drawing->getRenderingFunction(), 'bmp not supported - use default');
    }
}
