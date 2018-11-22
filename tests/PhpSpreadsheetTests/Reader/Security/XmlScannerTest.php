<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
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
        $reader = new XmlScanner();
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

        $reader = new XmlScanner();
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
}
