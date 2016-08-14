<?php

namespace PhpSpreadsheet\Tests\Style;

use PHPExcel\Style\Color;

class ColorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerColorGetRed
     */
    public function testGetRed()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Color::class,'getRed'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Style/ColorGetRed.data');
    }

    /**
     * @dataProvider providerColorGetGreen
     */
    public function testGetGreen()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Color::class,'getGreen'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetGreen()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Style/ColorGetGreen.data');
    }

    /**
     * @dataProvider providerColorGetBlue
     */
    public function testGetBlue()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Color::class,'getBlue'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetBlue()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Style/ColorGetBlue.data');
    }

    /**
     * @dataProvider providerColorChangeBrightness
     */
    public function testChangeBrightness()
    {
        list($args, $expectedResult) = func_get_args();
        $result = call_user_func_array(array(Color::class,'changeBrightness'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorChangeBrightness()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIteratorJson('rawTestData/Style/ColorChangeBrightness.json');
    }
}
