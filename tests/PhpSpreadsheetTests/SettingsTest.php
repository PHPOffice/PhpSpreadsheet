<?php

namespace PhpOffice\PhpSpreadsheetTests;

class SettingsTest extends \PHPUnit_Framework_TestCase
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
        $result = \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR) & $result));
        $this->assertFalse(libxml_disable_entity_loader());
    }

    public function testSetXMLSettings()
    {
        \PhpOffice\PhpSpreadsheet\Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID);
        $result = \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
        $this->assertFalse(libxml_disable_entity_loader());
    }
}
