<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @dataProvider providerInvalidSimpleXML
     *
     * @param $filename
     */
    public function testInvalidSimpleXML($filename): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $xmlReader = new Xml();
        $xmlReader->trySimpleXMLLoadString($filename);
    }

    public function providerInvalidSimpleXML()
    {
        $tests = [];
        foreach (glob('tests/data/Reader/Xml/XEETestInvalidSimpleXML*.xml') as $file) {
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
    }
}
