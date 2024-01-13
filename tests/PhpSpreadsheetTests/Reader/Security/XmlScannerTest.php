<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;
use XMLReader;

class XmlScannerTest extends TestCase
{
    /**
     * @dataProvider providerValidXML
     */
    public function testValidXML(string $filename, string $expectedResult): void
    {
        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerValidXML(): array
    {
        $tests = [];
        $glob = glob('tests/data/Reader/Xml/XEETestValid*.xml');
        self::assertNotFalse($glob);
        foreach ($glob as $file) {
            $filename = realpath($file);
            $expectedResult = file_get_contents($file);
            $tests[basename($file)] = [$filename, $expectedResult];
        }

        return $tests;
    }

    /**
     * @dataProvider providerInvalidXML
     */
    public function testInvalidXML(string $filename): void
    {
        $this->expectException(ReaderException::class);

        $reader = XmlScanner::getInstance(new \PhpOffice\PhpSpreadsheet\Reader\Xml());
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->scanFile($filename);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerInvalidXML(): array
    {
        $tests = [];
        $glob = glob('tests/data/Reader/Xml/XEETestInvalidUTF*.xml');
        self::assertNotFalse($glob);
        foreach ($glob as $file) {
            $filename = realpath($file);
            $tests[basename($file)] = [$filename];
        }

        return $tests;
    }

    public function testGetSecurityScannerForXmlBasedReader(): void
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecurityScanner();

        //    Must return an object...
        self::assertIsObject($scanner);
        //    ... of the correct type
        self::assertInstanceOf(XmlScanner::class, $scanner);
    }

    public function testGetSecurityScannerForNonXmlBasedReader(): void
    {
        $fileReader = new Xls();
        $scanner = $fileReader->getSecurityScanner();
        //    Must return a null...
        self::assertNull($scanner);
    }

    public function testGetSecurityScannerForNonXmlBasedReader2(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Security scanner is unexpectedly null');
        $fileReader = new Xls();
        $fileReader->getSecurityScannerOrThrow();
    }

    /**
     * @dataProvider providerValidXMLForCallback
     */
    public function testSecurityScanWithCallback(string $filename, string $expectedResult): void
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecurityScannerOrThrow();
        $scanner->setAdditionalCallback('strrev');
        $xml = $scanner->scanFile($filename);

        self::assertEquals(strrev($expectedResult), $xml);
    }

    public static function providerValidXMLForCallback(): array
    {
        $tests = [];
        $glob = glob('tests/data/Reader/Xml/SecurityScannerWithCallback*.xml');
        self::assertNotFalse($glob);
        foreach ($glob as $file) {
            $tests[basename($file)] = [realpath($file), file_get_contents($file)];
        }

        return $tests;
    }

    public function testLibxmlDisableEntityLoaderIsRestoredWithoutShutdown(): void
    {
        $reader = new Xlsx();
        unset($reader);

        $reader = new XMLReader();
        $opened = $reader->open('tests/data/Reader/Xml/SecurityScannerWithCallbackExample.xml');
        self::assertTrue($opened);
    }

    public function testEncodingAllowsMixedCase(): void
    {
        $scanner = new XmlScanner();
        $output = $scanner->scan($input = '<?xml version="1.0" encoding="utf-8"?><foo>bar</foo>');
        self::assertSame($input, $output);
    }
}
