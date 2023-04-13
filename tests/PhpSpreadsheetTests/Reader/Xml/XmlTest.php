<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
        if (method_exists($this, 'setOutputCallback')) {
            $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);
            self::assertFalse($xmlReader->trySimpleXMLLoadString($filename));
        }

        self::assertFalse(@$xmlReader->trySimpleXMLLoadString($filename));
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
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $xmlReader = new Xml();
        $spreadsheet = /** @scrutinizer ignore-unhandled */ @$xmlReader->load('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotSame('', $spreadsheet->getID());
    }

    public function testListWorksheetNamesCorruptedFile(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $xmlReader = new Xml();
        $names = /** @scrutinizer ignore-unhandled */ @$xmlReader->listWorksheetNames('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotEmpty($names);
    }

    public function testListWorksheetInfoCorruptedFile(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $xmlReader = new Xml();
        $info = /** @scrutinizer ignore-unhandled */ @$xmlReader->listWorksheetInfo('tests/data/Reader/Xml/CorruptedXmlFile.xml');
        self::assertNotEmpty($info);
    }
}
