<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class XmlScannerTest extends TestCase
{
    /**
     * @dataProvider providerValidXML
     *
     * @param mixed $filename
     * @param mixed $expectedResult
     */
    public function testValidXML($filename, $expectedResult)
    {
        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public function providerValidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../../data/Reader/Xml/XEETestValid*.xml') as $file) {
            $tests[basename($file)] = [realpath($file), file_get_contents($file)];
        }

        return $tests;
    }

    /**
     * @dataProvider providerInvalidXML
     *
     * @param mixed $filename
     */
    public function testInvalidXML($filename)
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public function providerInvalidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../../data/Reader/Xml/XEETestInvalidUTF*.xml') as $file) {
            $tests[basename($file)] = [realpath($file)];
        }

        return $tests;
    }

    public function testGetSecurityScannerForXmlBasedReader()
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecuritySCanner();

        //    Must return an object...
        $this->assertTrue(is_object($scanner));
        //    ... of the correct type
        $this->assertInstanceOf(XmlScanner::class, $scanner);
    }

    public function testGetSecurityScannerForNonXmlBasedReader()
    {
        $fileReader = new Xls();
        $scanner = $fileReader->getSecuritySCanner();
        //    Must return a null...
        $this->assertNull($scanner);
    }
}
