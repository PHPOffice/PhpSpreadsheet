<?php

namespace PHPExcel\Calculation;

require_once 'testDataFileIterator.php';

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
     * @dataProvider providerIS_BLANK
     */
    public function testIS_BLANK()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isBlank'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_BLANK()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_BLANK.data');
    }

    /**
     * @dataProvider providerIS_ERR
     */
    public function testIS_ERR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','IS_ERR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ERR()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_ERR.data');
    }

    /**
     * @dataProvider providerIS_ERROR
     */
    public function testIS_ERROR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','IS_ERROR'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ERROR()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_ERROR.data');
    }

    /**
     * @dataProvider providerERROR_TYPE
     */
    public function testERROR_TYPE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','errorType'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerERROR_TYPE()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/ERROR_TYPE.data');
    }

    /**
     * @dataProvider providerIS_LOGICAL
     */
    public function testIS_LOGICAL()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isLogical'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_LOGICAL()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_LOGICAL.data');
    }

    /**
     * @dataProvider providerIS_NA
     */
    public function testIS_NA()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isError('), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NA()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_NA.data');
    }

    /**
     * @dataProvider providerIS_NUMBER
     */
    public function testIS_NUMBER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isNumber'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NUMBER()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_NUMBER.data');
    }

    /**
     * @dataProvider providerIS_TEXT
     */
    public function testIS_TEXT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_TEXT()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_TEXT.data');
    }

    /**
     * @dataProvider providerIS_NONTEXT
     */
    public function testIS_NONTEXT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isNonText'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_NONTEXT()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_NONTEXT.data');
    }

    /**
     * @dataProvider providerIS_EVEN
     */
    public function testIS_EVEN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isEven'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_EVEN()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_EVEN.data');
    }

    /**
     * @dataProvider providerIS_ODD
     */
    public function testIS_ODD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','isOdd'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIS_ODD()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/IS_ODD.data');
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
        return new \testDataFileIterator('rawTestData/Calculation/Functions/TYPE.data');
    }

    /**
     * @dataProvider providerN
     */
    public function testN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Calculation\Functions','N'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return new \testDataFileIterator('rawTestData/Calculation/Functions/N.data');
    }
}
