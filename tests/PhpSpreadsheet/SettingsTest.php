<?php

namespace PhpSpreadsheet\Tests;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     */
    public function testGetXMLSettings()
    {
        $result = call_user_func([\PhpSpreadsheet\Settings::class, 'getLibXmlLoaderOptions']);
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR) & $result));
    }

    /**
     */
    public function testSetXMLSettings()
    {
        call_user_func_array([\PhpSpreadsheet\Settings::class, 'setLibXmlLoaderOptions'], [LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID]);
        $result = call_user_func([\PhpSpreadsheet\Settings::class, 'getLibXmlLoaderOptions']);
        $this->assertTrue((bool) ((LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_DTDVALID) & $result));
    }
}
