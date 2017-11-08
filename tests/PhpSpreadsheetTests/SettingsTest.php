<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    /**
     * @var string
     */
    protected $prevValue;

    public function setUp()
    {
        $this->prevValue = libxml_disable_entity_loader();
        libxml_disable_entity_loader(false); // Enable entity loader
    }

    protected function tearDown()
    {
        libxml_disable_entity_loader($this->prevValue);
    }

    public function testGetXMLSettings()
    {
        $result = Settings::getLibXmlLoaderOptions();
        self::assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR) & $result));
        self::assertFalse(libxml_disable_entity_loader());
    }

    public function testSetXMLSettings()
    {
        Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID);
        $result = Settings::getLibXmlLoaderOptions();
        self::assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
        self::assertFalse(libxml_disable_entity_loader());
    }
}
