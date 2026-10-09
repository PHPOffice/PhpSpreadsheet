<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Metafile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use ZipArchive;

class MetafileTest extends AbstractFunctional
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

    private static function createSpreadsheet(string $file): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $drawing = new Drawing();
        $drawing->setName('Metafile');
        $drawing->setPath(self::DIRECTORY . $file);
        $drawing->setCoordinates('B2');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        return $spreadsheet;
    }

    private static function getZipContents(string $filename, string $name): string
    {
        $zip = new ZipArchive();
        $zip->open($filename);
        $contents = (string) $zip->getFromName($name);
        $zip->close();

        return $contents;
    }

    #[DataProvider('providerMetafiles')]
    public function testXlsx(string $file, string $type, string $extension, string $mimeType): void
    {
        $spreadsheet = self::createSpreadsheet($file);
        $filename = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filename);

        // Metafiles are stored natively in Xlsx files
        $contentTypes = self::getZipContents($filename, '[Content_Types].xml');
        self::assertStringContainsString('<Default Extension="' . $extension . '" ContentType="' . $mimeType . '"/>', $contentTypes);
        $drawingXml = self::getZipContents($filename, 'xl/drawings/_rels/drawing1.xml.rels');
        self::assertMatchesRegularExpression('~Target="../media/[0-9a-f]{32}\.' . $extension . '"~', $drawingXml);

        $reloadedSpreadsheet = (new XlsxReader())->load($filename);
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame($type, $drawing->getMetafileType());
        self::assertSame($extension, $drawing->getExtension());
        self::assertSame('B2', $drawing->getCoordinates());
        self::assertSame(file_get_contents(self::DIRECTORY . $file), $drawing->getContents());
        unlink($filename);

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerMetafiles')]
    public function testOds(string $file): void
    {
        $spreadsheet = self::createSpreadsheet($file);
        $original = $spreadsheet->getActiveSheet()->getDrawingCollection()[0];
        self::assertInstanceOf(Drawing::class, $original);
        $filename = File::temporaryFilename();
        $writer = new OdsWriter($spreadsheet);
        self::assertTrue($writer->getConvertMetafilesToPng());
        $writer->save($filename);

        // By default, metafiles are converted to PNG in Ods files
        $manifest = self::getZipContents($filename, 'META-INF/manifest.xml');
        self::assertStringContainsString('manifest:full-path="Pictures/image1.png" manifest:media-type="image/png"', $manifest);
        self::assertStringContainsString('xlink:href="Pictures/image1.png"', self::getZipContents($filename, 'content.xml'));
        $size = getimagesizefromstring(self::getZipContents($filename, 'Pictures/image1.png'));
        self::assertNotFalse($size);
        self::assertSame([$original->getImageWidth(), $original->getImageHeight(), IMAGETYPE_PNG], [$size[0], $size[1], $size[2]]);

        $reloadedSpreadsheet = (new OdsReader())->load($filename);
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertFalse($drawing->isMetafile());
        self::assertSame('png', $drawing->getExtension());
        unlink($filename);

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * A metafile which can not be rendered is stored as is.
     */
    public function testOdsUnsupportedMetafile(): void
    {
        $spreadsheet = self::createSpreadsheet('metafile_unsupported.emf');
        $filename = File::temporaryFilename();
        $writer = new OdsWriter($spreadsheet);
        $writer->save($filename);

        $manifest = self::getZipContents($filename, 'META-INF/manifest.xml');
        self::assertStringContainsString('manifest:full-path="Pictures/image1.emf" manifest:media-type="image/x-emf"', $manifest);
        self::assertStringContainsString('xlink:href="Pictures/image1.emf"', self::getZipContents($filename, 'content.xml'));
        self::assertSame(file_get_contents(self::DIRECTORY . 'metafile_unsupported.emf'), self::getZipContents($filename, 'Pictures/image1.emf'));
        unlink($filename);

        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerMetafiles')]
    public function testOdsMetafilesAsIs(string $file, string $type, string $extension, string $mimeType): void
    {
        $spreadsheet = self::createSpreadsheet($file);
        $filename = File::temporaryFilename();
        $writer = new OdsWriter($spreadsheet);
        self::assertSame($writer, $writer->setConvertMetafilesToPng(false));
        self::assertFalse($writer->getConvertMetafilesToPng());
        $writer->save($filename);

        // Metafiles are stored as is in Ods files
        $manifest = self::getZipContents($filename, 'META-INF/manifest.xml');
        self::assertStringContainsString('manifest:full-path="Pictures/image1.' . $extension . '" manifest:media-type="' . $mimeType . '"', $manifest);
        self::assertStringContainsString('xlink:href="Pictures/image1.' . $extension . '"', self::getZipContents($filename, 'content.xml'));

        $reloadedSpreadsheet = (new OdsReader())->load($filename);
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame($type, $drawing->getMetafileType());
        self::assertSame(file_get_contents(self::DIRECTORY . $file), $drawing->getContents());
        unlink($filename);

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerMetafiles')]
    public function testXls(string $file): void
    {
        $spreadsheet = self::createSpreadsheet($file);
        $original = $spreadsheet->getActiveSheet()->getDrawingCollection()[0];
        self::assertNotNull($original);

        // Metafiles are converted to PNG in Xls files
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(MemoryDrawing::class, $drawing);
        self::assertSame(MemoryDrawing::MIMETYPE_PNG, $drawing->getMimeType());
        $image = $drawing->getImageResource();
        self::assertNotNull($image);
        self::assertSame($original->getImageWidth(), imagesx($image));
        self::assertSame($original->getImageHeight(), imagesy($image));

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerMetafiles')]
    public function testHtml(string $file): void
    {
        $spreadsheet = self::createSpreadsheet($file);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();

        // Browsers can not display metafiles : they are embedded as PNG
        self::assertMatchesRegularExpression('~<img [^>]*src="data:image/png;base64,[^"]+"~', $html);
        self::assertStringNotContainsString(self::DIRECTORY, $html);

        $spreadsheet->disconnectWorksheets();
    }

    public function testXlsxCommentBackground(): void
    {
        $spreadsheet = new Spreadsheet();
        $drawing = new Drawing();
        $drawing->setPath(self::DIRECTORY . 'metafile.emf');
        $comment = $spreadsheet->getActiveSheet()->getComment('A1');
        $comment->getText()->createText('Comment');
        $comment->setBackgroundImage($drawing);

        $filename = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filename);

        // Comment backgrounds are converted to PNG
        $png = self::getZipContents($filename, 'xl/media/' . $drawing->getMediaFilename());
        $size = getimagesizefromstring($png);
        self::assertNotFalse($size);
        self::assertSame(IMAGETYPE_PNG, $size[2]);
        unlink($filename);

        $spreadsheet->disconnectWorksheets();
    }
}
