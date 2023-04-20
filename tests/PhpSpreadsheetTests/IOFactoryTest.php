<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PHPUnit\Framework\TestCase;

class IOFactoryTest extends TestCase
{
    /**
     * @dataProvider providerCreateWriter
     *
     * @param string $name
     * @param string $expected
     */
    public function testCreateWriter($name, $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $actual = IOFactory::createWriter($spreadsheet, $name);
        self::assertSame($expected, get_class($actual));
    }

    public static function providerCreateWriter(): array
    {
        return [
            ['Xls', Writer\Xls::class],
            ['Xlsx', Writer\Xlsx::class],
            ['Ods', Writer\Ods::class],
            ['Csv', Writer\Csv::class],
            ['Html', Writer\Html::class],
            ['Mpdf', Writer\Pdf\Mpdf::class],
            ['Tcpdf', Writer\Pdf\Tcpdf::class],
            ['Dompdf', Writer\Pdf\Dompdf::class],
        ];
    }

    public function testRegisterWriter(): void
    {
        IOFactory::registerWriter('Pdf', Writer\Pdf\Mpdf::class);
        $spreadsheet = new Spreadsheet();
        $actual = IOFactory::createWriter($spreadsheet, 'Pdf');
        self::assertInstanceOf(Writer\Pdf\Mpdf::class, $actual);
    }

    /**
     * @dataProvider providerCreateReader
     *
     * @param string $name
     * @param string $expected
     */
    public function testCreateReader($name, $expected): void
    {
        $actual = IOFactory::createReader($name);
        self::assertSame($expected, get_class($actual));
    }

    public static function providerCreateReader(): array
    {
        return [
            ['Xls', Reader\Xls::class],
            ['Xlsx', Reader\Xlsx::class],
            ['Xml', Reader\Xml::class],
            ['Ods', Reader\Ods::class],
            ['Gnumeric', Reader\Gnumeric::class],
            ['Csv', Reader\Csv::class],
            ['Slk', Reader\Slk::class],
            ['Html', Reader\Html::class],
        ];
    }

    public function testRegisterReader(): void
    {
        IOFactory::registerReader('Custom', Reader\Html::class);
        $actual = IOFactory::createReader('Custom');
        self::assertInstanceOf(Reader\Html::class, $actual);
    }

    /**
     * @dataProvider providerIdentify
     */
    public function testIdentifyCreateLoad(string $file, string $expectedName, string $expectedClass): void
    {
        $actual = IOFactory::identify($file);
        self::assertSame($expectedName, $actual);
        $actual = IOFactory::createReaderForFile($file);
        self::assertSame($expectedClass, get_class($actual));
        $actual = IOFactory::load($file);
        self::assertInstanceOf(Spreadsheet::class, $actual);
    }

    public static function providerIdentify(): array
    {
        return [
            ['samples/templates/26template.xlsx', 'Xlsx', Reader\Xlsx::class],
            ['samples/templates/GnumericTest.gnumeric', 'Gnumeric', Reader\Gnumeric::class],
            ['samples/templates/old.gnumeric', 'Gnumeric', Reader\Gnumeric::class],
            ['samples/templates/30template.xls', 'Xls', Reader\Xls::class],
            ['samples/templates/OOCalcTest.ods', 'Ods', Reader\Ods::class],
            ['samples/templates/SylkTest.slk', 'Slk', Reader\Slk::class],
            ['samples/templates/excel2003.xml', 'Xml', Reader\Xml::class],
            // Following not readable by Excel.
            //['samples/templates/Excel2003XMLTest.xml', 'Xml', Reader\Xml::class],
            ['samples/templates/46readHtml.html', 'Html', Reader\Html::class],
        ];
    }

    public function testIdentifyInvalid(): void
    {
        $file = __DIR__ . '/../data/Reader/NotASpreadsheetFile.doc';

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to identify a reader for this file');
        IOFactory::identify($file);
    }

    public function testCreateInvalid(): void
    {
        $file = __DIR__ . '/../data/Reader/NotASpreadsheetFile.doc';

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to identify a reader for this file');
        IOFactory::createReaderForFile($file);
    }

    public function testLoadInvalid(): void
    {
        $file = __DIR__ . '/../data/Reader/NotASpreadsheetFile.doc';

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unable to identify a reader for this file');
        IOFactory::load($file);
    }

    public function testFormatAsExpected(): void
    {
        $fileName = 'samples/templates/30template.xls';

        $actual = IOFactory::identify($fileName, [IOFactory::READER_XLS]);
        self::assertSame('Xls', $actual);
    }

    public function testFormatNotAsExpectedThrowsException(): void
    {
        $fileName = 'samples/templates/30template.xls';

        $this->expectException(ReaderException::class);
        IOFactory::identify($fileName, [IOFactory::READER_ODS]);
    }

    public function testIdentifyNonExistingFileThrowException(): void
    {
        $this->expectException(ReaderException::class);

        IOFactory::identify('/non/existing/file');
    }

    public function testIdentifyExistingDirectoryThrowExceptions(): void
    {
        $this->expectException(ReaderException::class);

        IOFactory::identify('.');
    }

    public function testRegisterInvalidWriter(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Writer\Exception::class);

        IOFactory::registerWriter('foo', 'bar');
    }

    public function testRegisterInvalidReader(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        IOFactory::registerReader('foo', 'bar');
    }

    public function testCreateInvalidWriter(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Writer\Exception::class);
        $spreadsheet = new Spreadsheet();
        IOFactory::createWriter($spreadsheet, 'bad');
    }

    public function testCreateInvalidReader(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);
        IOFactory::createReader('bad');
    }

    public function testCreateReaderUnknownExtension(): void
    {
        $filename = 'samples/Reader/sampleData/example1.tsv';
        $reader = IOFactory::createReaderForFile($filename);
        self::assertEquals('PhpOffice\\PhpSpreadsheet\\Reader\\Csv', get_class($reader));
    }

    public function testCreateReaderCsvExtension(): void
    {
        $filename = 'samples/Reader/sampleData/example1.csv';
        $reader = IOFactory::createReaderForFile($filename);
        self::assertEquals('PhpOffice\\PhpSpreadsheet\\Reader\\Csv', get_class($reader));
    }

    public function testCreateReaderNoExtension(): void
    {
        $filename = 'samples/Reader/sampleData/example1xls';
        $reader = IOFactory::createReaderForFile($filename);
        self::assertEquals('PhpOffice\\PhpSpreadsheet\\Reader\\Xls', get_class($reader));
    }

    public function testCreateReaderNotSpreadsheet(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);
        $filename = __FILE__;
        IOFactory::createReaderForFile($filename);
    }
}
