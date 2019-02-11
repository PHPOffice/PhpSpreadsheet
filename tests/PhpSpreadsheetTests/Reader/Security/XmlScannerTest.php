<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlScannerTest extends TestCase
{
    /**
     * @dataProvider providerValidXML
     *
     * @param mixed $filename
     * @param mixed $expectedResult
     * @param $libxmlDisableEntityLoader
     */
    public function testValidXML($filename, $expectedResult, $libxmlDisableEntityLoader)
    {
        libxml_disable_entity_loader($libxmlDisableEntityLoader);

        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
        self::assertEquals($libxmlDisableEntityLoader, libxml_disable_entity_loader());
    }

    public function providerValidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../../data/Reader/Xml/XEETestValid*.xml') as $file) {
            $filename = realpath($file);
            $expectedResult = file_get_contents($file);
            $tests[basename($file) . '_libxml_entity_loader_disabled'] = [$filename, $expectedResult, true];
            $tests[basename($file) . '_libxml_entity_loader_enabled'] = [$filename, $expectedResult, false];
        }

        return $tests;
    }

    /**
     * @dataProvider providerInvalidXML
     *
     * @param mixed $filename
     * @param $libxmlDisableEntityLoader
     */
    public function testInvalidXML($filename, $libxmlDisableEntityLoader)
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);

        libxml_disable_entity_loader($libxmlDisableEntityLoader);

        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
        self::assertEquals($libxmlDisableEntityLoader, libxml_disable_entity_loader());
    }

    public function providerInvalidXML()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../../data/Reader/Xml/XEETestInvalidUTF*.xml') as $file) {
            $filename = realpath($file);
            $tests[basename($file) . '_libxml_entity_loader_disabled'] = [$filename, true];
            $tests[basename($file) . '_libxml_entity_loader_enabled'] = [$filename, false];
        }

        return $tests;
    }

    public function testGetSecurityScannerForXmlBasedReader()
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecuritySCanner();

        //    Must return an object...
        $this->assertInternalType('object', $scanner);
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

    /**
     * @dataProvider providerValidXMLForCallback
     *
     * @param mixed $filename
     * @param mixed $expectedResult
     */
    public function testSecurityScanWithCallback($filename, $expectedResult)
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecuritySCanner();
        $scanner->setAdditionalCallback('strrev');
        $xml = $scanner->scanFile($filename);

        $this->assertEquals(strrev($expectedResult), $xml);
    }

    public function providerValidXMLForCallback()
    {
        $tests = [];
        foreach (glob(__DIR__ . '/../../../data/Reader/Xml/SecurityScannerWithCallback*.xml') as $file) {
            $tests[basename($file)] = [realpath($file), file_get_contents($file)];
        }

        return $tests;
    }

    /**
     * @dataProvider providerLibxmlSettings
     *
     * @param $libxmlDisableLoader
     */
    public function testNewInstanceCreationDoesntChangeLibxmlSettings($libxmlDisableLoader)
    {
        libxml_disable_entity_loader($libxmlDisableLoader);

        $reader = new Xml();
        self::assertEquals($libxmlDisableLoader, libxml_disable_entity_loader($libxmlDisableLoader));
        unset($reader);
    }

    public function providerLibxmlSettings()
    {
        return [
            [true],
            [false],
        ];
    }
}
