<?php

namespace PhpSpreadsheet\Tests\Calculation;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        \PHPExcel\Calculation\Functions::setCompatibilityMode(\PHPExcel\Calculation\Functions::COMPATIBILITY_EXCEL);
    }

    public function testDUMMY()
    {
        $result = \PHPExcel\Calculation\Functions::DUMMY();
        $this->assertEquals('#Not Yet Implemented', $result);
    }

    public function testDIV0()
    {
        $result = \PHPExcel\Calculation\Functions::DIV0();
        $this->assertEquals('#DIV/0!', $result);
    }

    public function testNA()
    {
        $result = \PHPExcel\Calculation\Functions::NA();
        $this->assertEquals('#N/A', $result);
    }

    public function testNAN()
    {
        $result = \PHPExcel\Calculation\Functions::NAN();
        $this->assertEquals('#NUM!', $result);
    }

    public function testNAME()
    {
        $result = \PHPExcel\Calculation\Functions::NAME();
        $this->assertEquals('#NAME?', $result);
    }

    public function testREF()
    {
        $result = \PHPExcel\Calculation\Functions::REF();
        $this->assertEquals('#REF!', $result);
    }

    public function testNULL()
    {
        $result = \PHPExcel\Calculation\Functions::null();
        $this->assertEquals('#NULL!', $result);
    }

    public function testVALUE()
    {
        $result = \PHPExcel\Calculation\Functions::VALUE();
        $this->assertEquals('#VALUE!', $result);
    }

    /**
     * @dataProvider providerIsBlank
     */
    public function testIsBlank()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isBlank'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsBlank()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_BLANK.data');
    }

    /**
     * @dataProvider providerIsErr
     */
    public function testIsErr()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isErr'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsErr()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_ERR.data');
    }

    /**
     * @dataProvider providerIsError
     */
    public function testIsError()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isError'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsError()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_ERROR.data');
    }

    /**
     * @dataProvider providerErrorType
     */
    public function testErrorType()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','errorType'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerErrorType()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/ERROR_TYPE.data');
    }

    /**
     * @dataProvider providerIsLogical
     */
    public function testIsLogical()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isLogical'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsLogical()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_LOGICAL.data');
    }

    /**
     * @dataProvider providerIsNa
     */
    public function testIsNa()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isNa'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNa()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_NA.data');
    }

    /**
     * @dataProvider providerIsNumber
     */
    public function testIsNumber()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isNumber'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNumber()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_NUMBER.data');
    }

    /**
     * @dataProvider providerIsText
     */
    public function testIsText()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsText()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_TEXT.data');
    }

    /**
     * @dataProvider providerIsNonText
     */
    public function testIsNonText()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isNonText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNonText()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_NONTEXT.data');
    }

    /**
     * @dataProvider providerIsEven
     */
    public function testIsEven()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isEven'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsEven()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_EVEN.data');
    }

    /**
     * @dataProvider providerIsOdd
     */
    public function testIsOdd()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isOdd'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsOdd()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/IS_ODD.data');
    }

    /**
     * @dataProvider providerTYPE
     */
    public function testTYPE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','TYPE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTYPE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/TYPE.data');
    }

    /**
     * @dataProvider providerN
     */
    public function testN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','n'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/Functions/N.data');
    }
}
