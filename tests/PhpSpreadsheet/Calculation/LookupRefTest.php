<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PhpSpreadsheet\Calculation\Functions;
use PhpSpreadsheet\Calculation\LookupRef;

/**
 * Class LookupRefTest
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
        $result = call_user_func_array([LookupRef::class, 'HLOOKUP'], $args);
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
        $result = call_user_func_array([LookupRef::class, 'VLOOKUP'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP()
    {
        return require 'data/Calculation/LookupRef/VLOOKUP.php';
    }
}
