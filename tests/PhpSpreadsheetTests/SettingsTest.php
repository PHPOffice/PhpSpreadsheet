<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SpException;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    /**
     * @var bool
     */
    private $prevValue;

    protected function setUp(): void
    {
        // php 8.+ deprecated libxml_disable_entity_loader() - It's on by default
        if (\PHP_VERSION_ID < 80000) {
            $this->prevValue = libxml_disable_entity_loader();
            libxml_disable_entity_loader(false); // Enable entity loader
        }
    }

    protected function tearDown(): void
    {
        // php 8.+ deprecated libxml_disable_entity_loader() - It's on by default
        if (\PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($this->prevValue);
        }
        Settings::setCache(null);
    }

    public function testGetXMLSettings(): void
    {
        $result = Settings::getLibXmlLoaderOptions();
        self::assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR) & $result));
        // php 8.+ deprecated libxml_disable_entity_loader() - It's on by default
        if (\PHP_VERSION_ID < 80000) {
            self::assertFalse(libxml_disable_entity_loader());
        }
    }

    public function testSetXMLSettings(): void
    {
        $original = Settings::getLibXmlLoaderOptions();
        Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID);
        $result = Settings::getLibXmlLoaderOptions();
        self::assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
        // php 8.+ deprecated libxml_disable_entity_loader() - It's on by default
        if (\PHP_VERSION_ID < 80000) {
            self::assertFalse(libxml_disable_entity_loader());
        }
        Settings::setLibXmlLoaderOptions($original);
    }

    public function testInvalidChartRenderer(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('Chart renderer must implement');
        Settings::setChartRenderer(self::class);
    }

    public function testInvalidRequestFactory(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('HTTP client must be configured');
        Settings::getRequestFactory();
    }

    public function testCache(): void
    {
        $cache1 = Settings::getCache();
        self::assertNotNull($cache1);
        Settings::setCache(null);
        $cache2 = Settings::getCache();
        self::assertEquals($cache1, $cache2);
        self::assertNotSame($cache1, $cache2);
        $array = ['A1' => 10, 'B2' => 20];
        $cache2->setMultiple($array);
        self::assertSame($array, $cache2->getMultiple(array_keys($array)));
        self::assertNull($cache2->get('C3'));
        $cache2->clear();
        self::assertNull($cache2->get('A1'));
        self::assertNull($cache2->get('B2'));
    }
}
