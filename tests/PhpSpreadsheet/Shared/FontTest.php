<?php

namespace PhpSpreadsheet\Tests\Shared;

class FontTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAutoSizeMethod()
    {
        $expectedResult = \PHPExcel\Shared\Font::AUTOSIZE_METHOD_APPROX;

        $result = call_user_func(array('\PHPExcel\Shared\Font','getAutoSizeMethod'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetAutoSizeMethod()
    {
        $autosizeMethodValues = array(
            \PHPExcel\Shared\Font::AUTOSIZE_METHOD_EXACT,
            \PHPExcel\Shared\Font::AUTOSIZE_METHOD_APPROX,
        );

        foreach ($autosizeMethodValues as $autosizeMethodValue) {
            $result = call_user_func(array('\PHPExcel\Shared\Font','setAutoSizeMethod'), $autosizeMethodValue);
            $this->assertTrue($result);
        }
    }

    public function testSetAutoSizeMethodWithInvalidValue()
    {
        $unsupportedAutosizeMethod = 'guess';

        $result = call_user_func(array('\PHPExcel\Shared\Font','setAutoSizeMethod'), $unsupportedAutosizeMethod);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerFontSizeToPixels
     */
    public function testFontSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Font','fontSizeToPixels'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/FontSizeToPixels.data');
    }

    /**
     * @dataProvider providerInchSizeToPixels
     */
    public function testInchSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Font','inchSizeToPixels'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerInchSizeToPixels()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/InchSizeToPixels.data');
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     */
    public function testCentimeterSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Font','centimeterSizeToPixels'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCentimeterSizeToPixels()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/CentimeterSizeToPixels.data');
    }
}
