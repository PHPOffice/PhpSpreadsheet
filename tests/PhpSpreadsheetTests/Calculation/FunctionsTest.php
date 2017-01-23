<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
     *
     * @param mixed $expectedResult
     */
    public function testIsBlank($expectedResult, ...$args)
    {
        $result = Functions::isBlank(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsBlank()
    {
        return require 'data/Calculation/Functions/IS_BLANK.php';
    }

    /**
     * @dataProvider providerIsErr
     *
     * @param mixed $expectedResult
     */
    public function testIsErr($expectedResult, ...$args)
    {
        $result = Functions::isErr(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsErr()
    {
        return require 'data/Calculation/Functions/IS_ERR.php';
    }

    /**
     * @dataProvider providerIsError
     *
     * @param mixed $expectedResult
     */
    public function testIsError($expectedResult, ...$args)
    {
        $result = Functions::isError(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsError()
    {
        return require 'data/Calculation/Functions/IS_ERROR.php';
    }

    /**
     * @dataProvider providerErrorType
     *
     * @param mixed $expectedResult
     */
    public function testErrorType($expectedResult, ...$args)
    {
        $result = Functions::errorType(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerErrorType()
    {
        return require 'data/Calculation/Functions/ERROR_TYPE.php';
    }

    /**
     * @dataProvider providerIsLogical
     *
     * @param mixed $expectedResult
     */
    public function testIsLogical($expectedResult, ...$args)
    {
        $result = Functions::isLogical(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsLogical()
    {
        return require 'data/Calculation/Functions/IS_LOGICAL.php';
    }

    /**
     * @dataProvider providerIsNa
     *
     * @param mixed $expectedResult
     */
    public function testIsNa($expectedResult, ...$args)
    {
        $result = Functions::isNa(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNa()
    {
        return require 'data/Calculation/Functions/IS_NA.php';
    }

    /**
     * @dataProvider providerIsNumber
     *
     * @param mixed $expectedResult
     */
    public function testIsNumber($expectedResult, ...$args)
    {
        $result = Functions::isNumber(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNumber()
    {
        return require 'data/Calculation/Functions/IS_NUMBER.php';
    }

    /**
     * @dataProvider providerIsText
     *
     * @param mixed $expectedResult
     */
    public function testIsText($expectedResult, ...$args)
    {
        $result = Functions::isText(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsText()
    {
        return require 'data/Calculation/Functions/IS_TEXT.php';
    }

    /**
     * @dataProvider providerIsNonText
     *
     * @param mixed $expectedResult
     */
    public function testIsNonText($expectedResult, ...$args)
    {
        $result = Functions::isNonText(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsNonText()
    {
        return require 'data/Calculation/Functions/IS_NONTEXT.php';
    }

    /**
     * @dataProvider providerIsEven
     *
     * @param mixed $expectedResult
     */
    public function testIsEven($expectedResult, ...$args)
    {
        $result = Functions::isEven(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsEven()
    {
        return require 'data/Calculation/Functions/IS_EVEN.php';
    }

    /**
     * @dataProvider providerIsOdd
     *
     * @param mixed $expectedResult
     */
    public function testIsOdd($expectedResult, ...$args)
    {
        $result = Functions::isOdd(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsOdd()
    {
        return require 'data/Calculation/Functions/IS_ODD.php';
    }

    /**
     * @dataProvider providerTYPE
     *
     * @param mixed $expectedResult
     */
    public function testTYPE($expectedResult, ...$args)
    {
        $result = Functions::TYPE(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerTYPE()
    {
        return require 'data/Calculation/Functions/TYPE.php';
    }

    /**
     * @dataProvider providerN
     *
     * @param mixed $expectedResult
     */
    public function testN($expectedResult, ...$args)
    {
        $result = Functions::n(...$args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return require 'data/Calculation/Functions/N.php';
    }
}
