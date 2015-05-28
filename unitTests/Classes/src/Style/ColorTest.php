<?php

require_once 'testDataFileIterator.php';
require_once 'testDataFileIteratorJson.php';

class ColorTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . '/Bootstrap.php');
    }

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
        return new testDataFileIterator('rawTestData/Style/ColorGetRed.data');
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
        return new testDataFileIterator('rawTestData/Style/ColorGetGreen.data');
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
        return new testDataFileIterator('rawTestData/Style/ColorGetBlue.data');
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
        return new testDataFileIteratorJson('rawTestData/Style/ColorChangeBrightness.json');
    }
}
