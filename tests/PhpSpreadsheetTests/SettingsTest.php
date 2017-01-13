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
        call_user_func_array([\PhpOffice\PhpSpreadsheet\Settings::class, 'setLibXmlLoaderOptions'], [LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID]);
        $result = \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
    }

    public function testReadingExternalXMLAfterGettingSettings()
    {
        \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions();
        $this->assertInstanceOf(\SimpleXMLElement::class, simplexml_load_file('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl'));
    }
}
