<?php

namespace PhpOffice\PhpSpreadsheetTests;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testGetXMLSettings()
    {
        $result = \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR) & $result));
    }

    public function testSetXMLSettings()
    {
        \PhpOffice\PhpSpreadsheet\Settings::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID);
        $result = \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
    }
}
