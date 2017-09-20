<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit_Framework_TestCase;

class XEEValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheetXEETest;

    /**
     * @return Spreadsheet
     */
    protected function loadXEETestFile()
    {
        if (!$this->spreadsheetXEETest) {
            $filename = __DIR__ . '/../../../samples/templates/Excel2003XMLTest.xml';

            // Load into this instance
            $reader = new Xml();
            $this->spreadsheetXEETest = $reader->loadIntoExisting($filename, new Spreadsheet());
        }

        return $this->spreadsheetXEETest;
    }

    /**
     * @dataProvider providerInvalidXML
     * @expectedException \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @param mixed $filename
     */
    public function testInvalidXML($filename)
    {
        $reader = $this->getMockForAbstractClass(BaseReader::class);
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->securityScanFile($filename);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerInvalidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../data/Reader/XEE/XEETestInvalid*.xml') as $file) {
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
        $this->assertEquals($expectedResult, $result);
    }

    public function providerValidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../data/Reader/XEE/XEETestValid*.xml') as $file) {
            $tests[basename($file)] = [realpath($file), file_get_contents($file)];
        }

        return $tests;
    }

    /**
     * Check if it can read XML Hyperlink correctly.
     */
    public function testReadHyperlinks()
    {
        $spreadsheet = $this->loadXEETestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell('L1');

        $this->assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        $this->assertEquals('PHPExcel', $hyperlink->getValue());
        $this->assertEquals('http://www.phpexcel.net/', $hyperlink->getHyperlink()->getUrl());
    }
}
