<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Shared\Metafile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MetafileDrawingTest extends TestCase
{
    private const DIRECTORY = 'tests/data/Worksheet/Metafile/';

    /** @return array<string, array{string, string, string, int, int}> */
    public static function providerMetafiles(): array
    {
        return [
            'wmf' => ['metafile.wmf', Metafile::TYPE_WMF, 'wmf', 90, 60],
            'emf' => ['metafile.emf', Metafile::TYPE_EMF, 'emf', 120, 80],
            'emf+' => ['metafile_emfplus.emf', Metafile::TYPE_EMFPLUS, 'emf', 120, 80],
        ];
    }

    #[DataProvider('providerMetafiles')]
    public function testDrawing(string $file, string $type, string $extension, int $width, int $height): void
    {
        $drawing = new Drawing();
        $drawing->setPath(self::DIRECTORY . $file);

        self::assertTrue($drawing->isMetafile());
        self::assertSame($type, $drawing->getMetafileType());
        self::assertSame($extension, $drawing->getExtension());
        self::assertSame($width, $drawing->getImageWidth());
        self::assertSame($height, $drawing->getImageHeight());
        self::assertSame($width, $drawing->getWidth());
        self::assertSame($height, $drawing->getHeight());
        self::assertSame(IMAGETYPE_UNKNOWN, $drawing->getType());
        self::assertSame(file_get_contents(self::DIRECTORY . $file), $drawing->getContents());

        // Metafiles are converted to PNG when needed (comment background, ...)
        self::assertTrue($drawing->isSupportedForSave());
        self::assertSame(IMAGETYPE_PNG, $drawing->getImageTypeForSave());
        self::assertSame('.png', $drawing->getImageFileExtensionForSave());
        self::assertSame('image/png', $drawing->getImageMimeType());
        self::assertStringEndsWith('.png', $drawing->getMediaFilename());
    }

    public function testDrawingUnsupportedMetafile(): void
    {
        // The metafile can not be rendered : its sizes stay unknown
        $drawing = new Drawing();
        $drawing->setPath(self::DIRECTORY . 'metafile_unsupported.emf');

        self::assertTrue($drawing->isMetafile());
        self::assertSame(Metafile::TYPE_EMF, $drawing->getMetafileType());
        self::assertSame('emf', $drawing->getExtension());
        self::assertSame(0, $drawing->getImageWidth());
        self::assertSame(0, $drawing->getImageHeight());
    }

    public function testContentsWithoutPath(): void
    {
        self::assertNull((new Drawing())->getContents());
    }

    public function testDrawingNotMetafile(): void
    {
        $drawing = new Drawing();
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');

        self::assertFalse($drawing->isMetafile());
        self::assertNull($drawing->getMetafileType());
        self::assertSame('png', $drawing->getExtension());
    }

    public function testMetafileTypeResetBySetPath(): void
    {
        $drawing = new Drawing();
        $drawing->setPath(self::DIRECTORY . 'metafile.emf');
        self::assertTrue($drawing->isMetafile());
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertFalse($drawing->isMetafile());
    }

    #[DataProvider('providerMetafiles')]
    public function testMemoryDrawing(string $file, string $type, string $extension, int $width, int $height): void
    {
        $drawing = MemoryDrawing::fromString((string) file_get_contents(self::DIRECTORY . $file));

        self::assertSame(MemoryDrawing::MIMETYPE_PNG, $drawing->getMimeType());
        self::assertSame(MemoryDrawing::RENDERING_PNG, $drawing->getRenderingFunction());
        $image = $drawing->getImageResource();
        self::assertNotNull($image);
        self::assertSame($width, imagesx($image));
        self::assertSame($height, imagesy($image));
    }

    public function testCommentBackground(): void
    {
        $spreadsheet = new Spreadsheet();
        $drawing = new Drawing();
        $drawing->setPath(self::DIRECTORY . 'metafile.emf');

        $comment = $spreadsheet->getActiveSheet()->getComment('A1');
        $comment->setBackgroundImage($drawing);
        self::assertTrue($comment->hasBackgroundImage());
        $comment->setSizeAsBackgroundImage();
        self::assertSame('90pt', $comment->getWidth());
        self::assertSame('60pt', $comment->getHeight());
        $spreadsheet->disconnectWorksheets();
    }
}
