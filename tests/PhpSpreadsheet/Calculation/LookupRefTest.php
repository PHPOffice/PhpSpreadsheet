<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PHPExcel\Calculation\Functions;
use PHPExcel\Calculation\LookupRef;

/**
 * Class LookupRefTest
 * @package PHPExcel\Calculation
 */
class LookupRefTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHLOOKUP
     * @group fail19
     */
    public function testHLOOKUP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(LookupRef::class,'HLOOKUP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHLOOKUP()
    {
        return require 'data/Calculation/LookupRef/HLOOKUP.php';
    }

    /**
     * @dataProvider providerVLOOKUP
     * @group fail19
     */
    public function testVLOOKUP()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(LookupRef::class,'VLOOKUP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP()
    {
        return require 'data/Calculation/LookupRef/VLOOKUP.php';
    }
}
