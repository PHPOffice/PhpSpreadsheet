<?php

namespace PhpSpreadsheet\Tests\Shared;

use PhpSpreadsheet\Shared\Font;

class FontTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAutoSizeMethod()
    {
        $expectedResult = Font::AUTOSIZE_METHOD_APPROX;

        $result = call_user_func([Font::class, 'getAutoSizeMethod']);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetAutoSizeMethod()
    {
        $autosizeMethodValues = [
            Font::AUTOSIZE_METHOD_EXACT,
            Font::AUTOSIZE_METHOD_APPROX,
        ];

        foreach ($autosizeMethodValues as $autosizeMethodValue) {
            $result = call_user_func([Font::class, 'setAutoSizeMethod'], $autosizeMethodValue);
            $this->assertTrue($result);
        }
    }

    public function testSetAutoSizeMethodWithInvalidValue()
    {
        $unsupportedAutosizeMethod = 'guess';

        $result = call_user_func([Font::class, 'setAutoSizeMethod'], $unsupportedAutosizeMethod);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerFontSizeToPixels
     */
    public function testFontSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Font::class, 'fontSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels()
    {
        return require 'data/Shared/FontSizeToPixels.php';
    }

    /**
     * @dataProvider providerInchSizeToPixels
     */
    public function testInchSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Font::class, 'inchSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerInchSizeToPixels()
    {
        return require 'data/Shared/InchSizeToPixels.php';
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     */
    public function testCentimeterSizeToPixels()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Font::class, 'centimeterSizeToPixels'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCentimeterSizeToPixels()
    {
        return require 'data/Shared/CentimeterSizeToPixels.php';
    }
}
