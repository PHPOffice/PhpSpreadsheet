<?php

namespace PhpSpreadsheet\Tests\Calculation;

/**
 * Class LookupRefTest
 * @package PHPExcel\Calculation
 */
class LookupRefTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        \PHPExcel\Calculation\Functions::setCompatibilityMode(\PHPExcel\Calculation\Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHLOOKUP
     * @group fail19
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/LookupRef/HLOOKUP.data');
    }

    /**
     * @dataProvider providerVLOOKUP
     * @group fail19
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/LookupRef/VLOOKUP.data');
    }
}
