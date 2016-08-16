<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PhpSpreadsheet\Calculation\Functions;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testDUMMY()
    {
        $result = Functions::DUMMY();
        $this->assertEquals('#Not Yet Implemented', $result);
    }

    public function testDIV0()
    {
        $result = Functions::DIV0();
        $this->assertEquals('#DIV/0!', $result);
    }

    public function testNA()
    {
        $result = Functions::NA();
        $this->assertEquals('#N/A', $result);
    }

    public function testNAN()
    {
        $result = Functions::NAN();
        $this->assertEquals('#NUM!', $result);
    }

    public function testNAME()
    {
        $result = Functions::NAME();
        $this->assertEquals('#NAME?', $result);
    }

    public function testREF()
    {
        $result = Functions::REF();
        $this->assertEquals('#REF!', $result);
    }

    public function testNULL()
    {
        $result = Functions::null();
        $this->assertEquals('#NULL!', $result);
    }

    public function testVALUE()
    {
        $result = Functions::VALUE();
        $this->assertEquals('#VALUE!', $result);
    }

    /**
     * @dataProvider providerIsBlank
     */
    public function testIsBlank()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isBlank'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsBlank()
    {
        return require 'data/Calculation/Functions/IS_BLANK.php';
    }

    /**
     * @dataProvider providerIsErr
     */
    public function testIsErr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isErr'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsErr()
    {
        return require 'data/Calculation/Functions/IS_ERR.php';
    }

    /**
     * @dataProvider providerIsError
     */
    public function testIsError()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isError'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsError()
    {
        return require 'data/Calculation/Functions/IS_ERROR.php';
    }

    /**
     * @dataProvider providerErrorType
     */
    public function testErrorType()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'errorType'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerErrorType()
    {
        return require 'data/Calculation/Functions/ERROR_TYPE.php';
    }

    /**
     * @dataProvider providerIsLogical
     */
    public function testIsLogical()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isLogical'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsLogical()
    {
        return require 'data/Calculation/Functions/IS_LOGICAL.php';
    }

    /**
     * @dataProvider providerIsNa
     */
    public function testIsNa()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isNa'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNa()
    {
        return require 'data/Calculation/Functions/IS_NA.php';
    }

    /**
     * @dataProvider providerIsNumber
     */
    public function testIsNumber()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isNumber'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNumber()
    {
        return require 'data/Calculation/Functions/IS_NUMBER.php';
    }

    /**
     * @dataProvider providerIsText
     */
    public function testIsText()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsText()
    {
        return require 'data/Calculation/Functions/IS_TEXT.php';
    }

    /**
     * @dataProvider providerIsNonText
     */
    public function testIsNonText()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isNonText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNonText()
    {
        return require 'data/Calculation/Functions/IS_NONTEXT.php';
    }

    /**
     * @dataProvider providerIsEven
     */
    public function testIsEven()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isEven'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsEven()
    {
        return require 'data/Calculation/Functions/IS_EVEN.php';
    }

    /**
     * @dataProvider providerIsOdd
     */
    public function testIsOdd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'isOdd'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsOdd()
    {
        return require 'data/Calculation/Functions/IS_ODD.php';
    }

    /**
     * @dataProvider providerTYPE
     */
    public function testTYPE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'TYPE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTYPE()
    {
        return require 'data/Calculation/Functions/TYPE.php';
    }

    /**
     * @dataProvider providerN
     */
    public function testN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Functions::class,'n'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return require 'data/Calculation/Functions/N.php';
    }
}
