<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Security;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use XMLReader;

class XmlScannerTest extends TestCase
{
    #[DataProvider('providerValidXML')]
    public function testValidXML(string $filename, string $expectedResult): void
    {
        $reader = XmlScanner::getInstance(new Xml());
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
            $expectedResult = (string) file_get_contents($file);
            if (preg_match('/UTF-16(LE|BE)?/', $file, $matches) == 1) {
                $expectedResult = (string) mb_convert_encoding($expectedResult, 'UTF-8', $matches[0]);
                $expectedResult = preg_replace('/encoding\s*=\s*[\'"]UTF-\d+(LE|BE)?[\'"]/', '', $expectedResult) ?? $expectedResult;
            }
            $tests[basename($file)] = [(string) $filename, $expectedResult];
        }

        return $tests;
    }

    #[DataProvider('providerInvalidXML')]
    public function testInvalidXML(string $filename): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessageMatches('/Detected use of ENTITY|UTF-7 encoding not permitted/');

        $reader = new Xml();
        $reader->load($filename);
    }

    #[DataProvider('providerInvalidXML')]
    public function testInvalidXMLString(string $filename): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessageMatches('/Detected use of ENTITY|UTF-7 encoding not permitted/');

        $reader = new Xml();
        $contents = file_get_contents($filename);
        self::assertNotFalse($contents);
        $reader->loadSpreadsheetFromString($contents);
    }

    public static function providerInvalidXML(): array
    {
        $tests = [];
        $glob = glob('tests/data/Reader/Xml/XEETestInvalidUTF*.xml');
        self::assertNotFalse($glob);
        foreach ($glob as $file) {
            $filename = realpath($file);
            $tests[basename($file)] = [(string) $filename];
        }

        return $tests;
    }

    public function testGetSecurityScannerForXmlBasedReader(): void
    {
        $fileReader = new Xlsx();
        $scanner = $fileReader->getSecurityScanner();

        //    Must return an object...
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

    #[DataProvider('providerValidXMLForCallback')]
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
            $tests[basename($file)] = [(string) realpath($file), (string) file_get_contents($file)];
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

    #[DataProvider('providerInvalidXlsx')]
    public function testInvalidXlsx(string $filename, string $message): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage($message);
        $reader = new Xlsx();
        $reader->load("tests/data/Reader/XLSX/$filename");
    }

    public static function providerInvalidXlsx(): array
    {
        return [
            ['utf7white.dontuse', 'UTF-7 encoding not permitted'],
            ['utf7quoteorder.dontuse', 'UTF-7 encoding not permitted'],
            ['utf8and16.dontuse', 'Double encoding not permitted'],
            ['utf8and16.entity.dontuse', 'Detected use of ENTITY'],
            ['utf8entity.dontuse', 'Detected use of ENTITY'],
            ['utf16entity.dontuse', 'Detected use of ENTITY'],
            ['ebcdic.dontuse', 'EBCDIC encoding not permitted'],
        ];
    }

    #[DataProvider('providerValidUtf16')]
    public function testValidUtf16(string $filename): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load("tests/data/Reader/XLSX/$filename");
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerValidUtf16(): array
    {
        return [
            ['utf16be.xlsx'],
            ['utf16be.bom.xlsx'],
        ];
    }
}
