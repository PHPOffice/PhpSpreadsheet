<?php

namespace PHPExcel\Style;

require_once 'testDataFileIterator.php';
require_once 'testDataFileIteratorJson.php';

class ColorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerColorGetRed
     */
    public function testGetRed()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\\PHPExcel\\Style\\Color','getRed'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Style/ColorGetRed.data');
    }

    /**
     * @dataProvider providerColorGetGreen
     */
    public function testGetGreen()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\\PHPExcel\\Style\\Color','getGreen'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetGreen()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Style/ColorGetGreen.data');
    }

    /**
     * @dataProvider providerColorGetBlue
     */
    public function testGetBlue()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\\PHPExcel\\Style\\Color','getBlue'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorGetBlue()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Style/ColorGetBlue.data');
    }

    /**
     * @dataProvider providerColorChangeBrightness
     */
    public function testChangeBrightness()
    {
        list($args, $expectedResult) = func_get_args();
        $result = call_user_func_array(array('\\PHPExcel\\Style\\Color','changeBrightness'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColorChangeBrightness()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIteratorJson('rawTestData/Style/ColorChangeBrightness.json');
    }
}
