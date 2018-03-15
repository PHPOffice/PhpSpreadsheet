<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @dataProvider providerInvalidXML
     *
     * @param mixed $filename
     */
    public function testInvalidXML($filename)
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $reader = $this->getMockForAbstractClass(BaseReader::class);
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->securityScanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public function providerInvalidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../data/Reader/Xml/XEETestInvalidUTF*.xml') as $file) {
            $tests[basename($file)] = [realpath($file)];
        }

        return $tests;
    }

    /**
     * @dataProvider providerInvalidSimpleXML
     *
     * @param $filename
     */
    public function testInvalidSimpleXML($filename)
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $xmlReader = new Xml();
        $xmlReader->trySimpleXMLLoadString($filename);
    }

    public function providerInvalidSimpleXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../data/Reader/Xml/XEETestInvalidSimpleXML*.xml') as $file) {
            $tests[basename($file)] = [realpath($file)];
        }

        return $tests;
    }

    /**
     * @dataProvider providerValidXML
     *
     * @param mixed $filename
     * @param mixed $expectedResult
     */
    public function testValidXML($filename, $expectedResult)
    {
        $reader = $this->getMockForAbstractClass(BaseReader::class);
        $result = $reader->securityScanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public function providerValidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../data/Reader/Xml/XEETestValid*.xml') as $file) {
            $tests[basename($file)] = [realpath($file), file_get_contents($file)];
        }

        return $tests;
    }

    /**
     * Check if it can read XML Hyperlink correctly.
     */
    public function testReadHyperlinks()
    {
        $reader = new Xml();
        $spreadsheet = $reader->load('../samples/templates/Excel2003XMLTest.xml');
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell('L1');

        self::assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('http://phpspreadsheet.readthedocs.io/', $hyperlink->getHyperlink()->getUrl());
    }

    public function testReadWithoutStyle()
    {
        $reader = new Xml();
        $spreadsheet = $reader->load(__DIR__ . '/../../data/Reader/Xml/WithoutStyle.xml');
        self::assertSame('Test String 1', $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
    }
}
