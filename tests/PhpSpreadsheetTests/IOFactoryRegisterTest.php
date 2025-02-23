<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

// Separate processes because register arrays are static
#[Attributes\RunTestsInSeparateProcesses]
class IOFactoryRegisterTest extends TestCase
{
    public function testRegisterWriter(): void
    {
        IOFactory::registerWriter('Pdf', Writer\Pdf\Mpdf::class);
        $spreadsheet = new Spreadsheet();
        $actual = IOFactory::createWriter($spreadsheet, 'Pdf');
        self::assertInstanceOf(Writer\Pdf\Mpdf::class, $actual);
    }

    public function testRegisterReader(): void
    {
        IOFactory::registerReader('Custom', Reader\Html::class);
        $actual = IOFactory::createReader('Custom');
        self::assertInstanceOf(Reader\Html::class, $actual);
    }

    public function testRegisterInvalidWriter(): void
    {
        $this->expectException(Writer\Exception::class);
        $this->expectExceptionMessage('writers must implement');
        IOFactory::registerWriter('foo', 'bar'); // @phpstan-ignore-line
    }

    public function testRegisterInvalidReader(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('readers must implement');
        IOFactory::registerReader('foo', 'bar'); // @phpstan-ignore-line
    }

    public static function testRegisterCustomReader(): void
    {
        IOFactory::registerReader(IOFactory::READER_XLSX, CustomReader::class);
        $inputFileName = 'tests/data/Reader/XLSX/1900_Calendar.xlsx';
        $loadSpreadsheet = IOFactory::load($inputFileName);
        $sheet = $loadSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $sheet->getCell('A1')->getFormattedValue());
        $loadSpreadsheet->disconnectWorksheets();

        $reader = new CustomReader();
        $newSpreadsheet = $reader->load($inputFileName);
        $newSheet = $newSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $newSheet->getCell('A1')->getFormattedValue());
        $newSpreadsheet->disconnectWorksheets();

        $inputFileType = IOFactory::identify($inputFileName, null, true);
        $objReader = IOFactory::createReader($inputFileType);
        self::assertInstanceOf(CustomReader::class, $objReader);
        $objSpreadsheet = $objReader->load($inputFileName);
        $objSheet = $objSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $objSheet->getCell('A1')->getFormattedValue());
        $objSpreadsheet->disconnectWorksheets();
    }

    public static function testRegisterCustomWriter(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new CustomWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style0 n">1</td>', $html);
        IOFactory::registerWriter(IOFactory::WRITER_HTML, CustomWriter::class);
        $objWriter = IOFactory::createWriter($spreadsheet, CustomWriter::class);
        self::assertInstanceOf(CustomWriter::class, $objWriter);
        $html2 = $objWriter->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style0 n">1</td>', $html2);
        $spreadsheet->disconnectWorksheets();
    }
}
