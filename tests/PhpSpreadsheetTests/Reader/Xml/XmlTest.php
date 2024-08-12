<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @dataProvider providerInvalidSimpleXML
     */
    public function testInvalidSimpleXML(string $filename): void
    {
        $xmlReader = new Xml();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid Spreadsheet file');
        $xmlReader->load($filename);
    }

    public static function providerInvalidSimpleXML(): array
    {
        $tests = [];
        $glob = glob('tests/data/Reader/Xml/XEETestInvalidSimpleXML*.xml');
        self::assertNotFalse($glob);
        foreach ($glob as $file) {
            $tests[basename($file)] = [realpath($file)];
        }

        return $tests;
    }

    /**
     * Check if it can read XML Hyperlink correctly.
     */
    public function testHyperlinksAltCharset(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load('tests/data/Reader/Xml/excel2003.iso8859-1.xml');
        $firstSheet = $spreadsheet->getSheet(0);
        self::assertSame('Voilà', $spreadsheet->getActiveSheet()->getCell('A1')->getValue());

        $hyperlink = $firstSheet->getCell('A2');

        self::assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('https://phpspreadsheet.readthedocs.io', $hyperlink->getHyperlink()->getUrl());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadCorruptedFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Cannot load invalid XML file');

        $xmlReader = new Xml();
        $spreadsheet = @$xmlReader->load('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotSame('', $spreadsheet->getID());
    }

    public function testListWorksheetNamesCorruptedFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Problem reading');

        $xmlReader = new Xml();
        $names = @$xmlReader->listWorksheetNames('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotEmpty($names);
    }

    public function testListWorksheetInfoCorruptedFile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Problem reading');

        $xmlReader = new Xml();
        $info = @$xmlReader->listWorksheetInfo('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotEmpty($info);
    }

    public function testInvalidXMLFromString(): void
    {
        $xmlReader = new Xml();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Cannot load invalid XML string: 0');
        $xmlReader->loadSpreadsheetFromString('0');
    }

    public function testInvalidXMLFromEmptyString(): void
    {
        $xmlReader = new Xml();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Cannot load invalid XML string: ');
        $xmlReader->loadSpreadsheetFromString('');
    }

    public function testEmptyFilename(): void
    {
        $xmlReader = new Xml();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('File "" does not exist');
        $xmlReader->load('');
    }
}
