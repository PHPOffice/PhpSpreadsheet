<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PhpSpreadsheet\Calculation\Functions;
use PhpSpreadsheet\Calculation\Logical;

class LogicalTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testTRUE()
    {
        $result = Logical::TRUE();
        $this->assertTrue($result);
    }

    public function testFALSE()
    {
        $result = Logical::FALSE();
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerAND
     */
    public function testAND()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Logical::class, 'logicalAnd'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerAND()
    {
        return require 'data/Calculation/Logical/AND.php';
    }

    /**
     * @dataProvider providerOR
     */
    public function testOR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Logical::class, 'logicalOr'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerOR()
    {
        return require 'data/Calculation/Logical/OR.php';
    }

    /**
     * @dataProvider providerNOT
     */
    public function testNOT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Logical::class, 'NOT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerNOT()
    {
        return require 'data/Calculation/Logical/NOT.php';
    }

    /**
     * @dataProvider providerIF
     */
    public function testIF()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Logical::class, 'statementIf'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIF()
    {
        return require 'data/Calculation/Logical/IF.php';
    }

    /**
     * @dataProvider providerIFERROR
     */
    public function testIFERROR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Logical::class, 'IFERROR'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIFERROR()
    {
        return require 'data/Calculation/Logical/IFERROR.php';
    }
}
