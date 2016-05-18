<?php

namespace PHPExcel\Calculation;

require_once 'testDataFileIterator.php';

class LookupRefTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        \PHPExcel\Calculation\Functions::setCompatibilityMode(\PHPExcel\Calculation\Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHLOOKUP
     */
    public function testHLOOKUP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\LookupRef','HLOOKUP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHLOOKUP()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/LookupRef/HLOOKUP.data');
    }

    /**
     * @dataProvider providerVLOOKUP
     */
    public function testVLOOKUP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\LookupRef','VLOOKUP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Calculation/LookupRef/VLOOKUP.data');
    }
}
