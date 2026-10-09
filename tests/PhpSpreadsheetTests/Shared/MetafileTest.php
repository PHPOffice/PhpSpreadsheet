<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Metafile;
use PhpOffice\WMF\Exception\WMFException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MetafileTest extends TestCase
{
    private const DIRECTORY = 'tests/data/Worksheet/Metafile/';

    /** @return array<string, array{string, string, string, string}> */
    public static function providerMetafiles(): array
    {
        return [
            'wmf' => ['metafile.wmf', Metafile::TYPE_WMF, 'wmf', 'image/x-wmf'],
            'emf' => ['metafile.emf', Metafile::TYPE_EMF, 'emf', 'image/x-emf'],
            'emf+' => ['metafile_emfplus.emf', Metafile::TYPE_EMFPLUS, 'emf', 'image/x-emf'],
        ];
    }

    #[DataProvider('providerMetafiles')]
    public function testDetect(string $file, string $type, string $extension, string $mimeType): void
    {
        $path = self::DIRECTORY . $file;
        self::assertSame($type, Metafile::detect((string) file_get_contents($path)));
        self::assertSame($type, Metafile::detectFile($path));
        self::assertSame($extension, Metafile::getExtension($type));
        self::assertSame($mimeType, Metafile::getMimeType($type));
    }

    public function testDetectNotMetafile(): void
    {
        self::assertNull(Metafile::detect('not a metafile'));
        self::assertNull(Metafile::detectFile('tests/data/Writer/XLSX/blue_square.png'));
        self::assertNull(Metafile::detectFile(self::DIRECTORY . 'missing.emf'));
    }

    #[DataProvider('providerMetafiles')]
    public function testToPng(string $file): void
    {
        $png = Metafile::toPng((string) file_get_contents(self::DIRECTORY . $file));
        $size = getimagesizefromstring($png);
        self::assertNotFalse($size);
        self::assertSame(IMAGETYPE_PNG, $size[2]);
        self::assertGreaterThan(0, $size[0]);
        self::assertGreaterThan(0, $size[1]);
    }

    public function testEmfPlusRecordsAreRendered(): void
    {
        // The EMF+ records draw a green rectangle, the EMF fallback records a blue one
        $image = Metafile::toGdImage((string) file_get_contents(self::DIRECTORY . 'metafile_emfplus.emf'));
        self::assertSame(0x00AA00, imagecolorat($image, 5, 5) & 0xFFFFFF);
    }

    public function testInvalidMetafile(): void
    {
        self::assertNull(Metafile::tryToGdImage(null));
        self::assertNull(Metafile::tryToGdImage('not a metafile'));
        self::assertNull(Metafile::tryToPng(null));
        self::assertNull(Metafile::tryToPng('not a metafile'));

        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Unable to render metafile');
        Metafile::toGdImage('not a metafile');
    }

    public function testTryToGdImage(): void
    {
        $image = Metafile::tryToGdImage((string) file_get_contents(self::DIRECTORY . 'metafile.emf'));
        self::assertNotNull($image);
        self::assertSame(120, imagesx($image));
        self::assertSame(80, imagesy($image));
    }

    /**
     * A EMF file with a record not implemented by phpoffice/wmf.
     */
    public function testUnsupportedRecord(): void
    {
        $content = (string) file_get_contents(self::DIRECTORY . 'metafile_unsupported.emf');
        self::assertSame(Metafile::TYPE_EMF, Metafile::detect($content));
        self::assertNull(Metafile::tryToGdImage($content));
        self::assertNull(Metafile::tryToPng($content));

        try {
            Metafile::toGdImage($content);
            self::fail('An exception should be thrown');
        } catch (PhpSpreadsheetException $e) {
            self::assertSame('Unable to render metafile: Reader : Function not implemented : 0x0072', $e->getMessage());
            self::assertInstanceOf(WMFException::class, $e->getPrevious());
        }
    }
}
