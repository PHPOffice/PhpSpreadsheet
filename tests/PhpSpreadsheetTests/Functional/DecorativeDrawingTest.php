<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use ZipArchive;

class DecorativeDrawingTest extends AbstractFunctional
{
    private static function spreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ([['B2', 'Logo', false], ['B10', 'Ornament', true]] as [$coordinates, $name, $decorative]) {
            $drawing = new Drawing();
            $drawing->setPath('tests/data/Writer/XLSX/blue_square.png')
                ->setCoordinates($coordinates)
                ->setName($name)
                ->setDescription($name)
                ->setDecorative($decorative)
                ->setWorksheet($sheet);
        }
        // Two cells anchor it, and it links
        $drawing = new Drawing();
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png')
            ->setCoordinates('D2')
            ->setCoordinates2('F6')
            ->setName('Divider')
            ->setDecorative(true)
            ->setWorksheet($sheet);
        $drawing->setHyperlink(new Hyperlink('https://example.com/'));

        return $spreadsheet;
    }

    /** @return array<string, bool> */
    private static function decorative(Spreadsheet $spreadsheet): array
    {
        $decorative = [];
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $drawing) {
            $decorative[$drawing->getName()] = $drawing->isDecorative();
        }
        ksort($decorative);

        return $decorative;
    }

    #[DataProvider('providerFormats')]
    public function testDecorativeSurvivesTheRoundTrip(string $format): void
    {
        $spreadsheetOld = self::spreadsheet();
        $spreadsheet = $this->writeAndReload($spreadsheetOld, $format);
        $spreadsheetOld->disconnectWorksheets();
        self::assertSame(['Divider' => true, 'Logo' => false, 'Ornament' => true], self::decorative($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerFormats(): array
    {
        return [['Xlsx'], ['Ods']];
    }

    public function testWrittenAsExcelAndLibreOfficeWriteIt(): void
    {
        $spreadsheet = self::spreadsheet();
        $xlsx = new XlsxWriter($spreadsheet);
        $drawing = $xlsx->getWriterPartDrawing()->writeDrawings($spreadsheet->getActiveSheet());
        self::assertSame(2, substr_count($drawing, '<a:extLst>'));
        // The schema order: the link first
        self::assertMatchesRegularExpression('#name="Divider"[^>]*><a:hlinkClick [^>]*/><a:extLst>#', $drawing);
        self::assertStringContainsString(
            '<xdr:cNvPr id="2" name="Ornament" descr="Ornament"><a:extLst><a:ext uri="{C183D7F6-B498-43B3-948B-1728B52AA6E4}">'
            . '<adec:decorative xmlns:adec="http://schemas.microsoft.com/office/drawing/2017/decorative" val="1"/></a:ext></a:extLst></xdr:cNvPr>',
            $drawing
        );

        $content = (new OdsWriter($spreadsheet))->getWriterPartContent()->write();
        self::assertStringContainsString(
            '<style:style style:name="gr1" style:family="graphic"/>'
            . '<style:style style:name="gr2" style:family="graphic"><style:graphic-properties'
            . ' xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0" loext:decorative="true"/></style:style>',
            $content
        );
        self::assertMatchesRegularExpression('/draw:name="Logo"[^>]* draw:style-name="gr1"/', $content);
        self::assertMatchesRegularExpression('/draw:name="Ornament"[^>]* draw:style-name="gr2"/', $content);
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadAsLibreOfficeWritesIt(): void
    {
        // Converted by LibreOffice 26.8 from a Xlsx with a plain and a decorative image
        $spreadsheet = (new OdsReader())->load('tests/data/Reader/Ods/DecorativeImage.ods');
        self::assertSame(['Logo' => false, 'Ornament' => true], self::decorative($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testNoDecorativeStyleWithoutDecorativeImages(): void
    {
        $spreadsheet = self::spreadsheet();
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $drawing) {
            $drawing->setDecorative(false);
        }
        $content = (new OdsWriter($spreadsheet))->getWriterPartContent()->write();
        self::assertStringContainsString('<style:style style:name="gr1" style:family="graphic"/>', $content);
        self::assertStringNotContainsString('gr2', $content);
        $spreadsheet->disconnectWorksheets();
    }

    public function testNoGraphicStylesWithoutImages(): void
    {
        $spreadsheet = new Spreadsheet();
        $content = (new OdsWriter($spreadsheet))->getWriterPartContent()->write();
        self::assertStringNotContainsString('style:family="graphic"', $content);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Write, change one part of the package, and read it back.
     */
    private function rewritten(IWriter $writer, string $part, string $search, string $replace): string
    {
        $file = File::temporaryFilename();
        $writer->save($file);
        $zip = new ZipArchive();
        $zip->open($file);
        $xml = (string) $zip->getFromName($part);
        self::assertStringContainsString($search, $xml);
        $zip->addFromString($part, str_replace($search, $replace, $xml));
        $zip->close();

        return $file;
    }

    public function testXlsxReadsTrue(): void
    {
        $spreadsheet = self::spreadsheet();
        $file = $this->rewritten(new XlsxWriter($spreadsheet), 'xl/drawings/drawing1.xml', 'val="1"', 'val="true"');
        $spreadsheet->disconnectWorksheets();
        $spreadsheet = (new XlsxReader())->load($file);
        unlink($file);
        self::assertSame(['Divider' => true, 'Logo' => false, 'Ornament' => true], self::decorative($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testOdsReadsTheFormOfOdf14(): void
    {
        $spreadsheet = self::spreadsheet();
        $file = $this->rewritten(new OdsWriter($spreadsheet), 'content.xml', 'loext:decorative="true"', 'draw:decorative="true"');
        $spreadsheet->disconnectWorksheets();
        $spreadsheet = (new OdsReader())->load($file);
        unlink($file);
        self::assertSame(['Divider' => true, 'Logo' => false, 'Ornament' => true], self::decorative($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testOdsReadsItWithReadDataOnly(): void
    {
        $reader = new OdsReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load('tests/data/Reader/Ods/DecorativeImage.ods');
        self::assertSame(['Logo' => false, 'Ornament' => true], self::decorative($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }
}
