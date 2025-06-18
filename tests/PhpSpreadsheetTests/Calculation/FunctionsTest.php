<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
    }

    public function tearDown()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
    }

    public function testCompatibilityMode()
    {
        $result = Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        // Test for a true response for success
        $this->assertTrue($result);
        // Test that mode has been changed
        $this->assertEquals(Functions::COMPATIBILITY_GNUMERIC, Functions::getCompatibilityMode());
    }

    public function testInvalidCompatibilityMode()
    {
        $result = Functions::setCompatibilityMode('INVALIDMODE');
        // Test for a false response for failure
        $this->assertFalse($result);
        // Test that mode has not been changed
        $this->assertEquals(Functions::COMPATIBILITY_EXCEL, Functions::getCompatibilityMode());
    }

    public function testReturnDateType()
    {
        $result = Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        // Test for a true response for success
        $this->assertTrue($result);
        // Test that mode has been changed
        $this->assertEquals(Functions::RETURNDATE_PHP_OBJECT, Functions::getReturnDateType());
    }

    public function testInvalidReturnDateType()
    {
        $result = Functions::setReturnDateType('INVALIDTYPE');
        // Test for a false response for failure
        $this->assertFalse($result);
        // Test that mode has not been changed
        $this->assertEquals(Functions::RETURNDATE_EXCEL, Functions::getReturnDateType());
    }

    public function testDUMMY()
    {
        $result = Functions::DUMMY();
        self::assertEquals('#Not Yet Implemented', $result);
    }

    public function testDIV0()
    {
        $result = Functions::DIV0();
        self::assertEquals('#DIV/0!', $result);
    }

    public function testNA()
    {
        $result = Functions::NA();
        self::assertEquals('#N/A', $result);
    }

    public function testNAN()
    {
        $result = Functions::NAN();
        self::assertEquals('#NUM!', $result);
    }

    public function testNAME()
    {
        $result = Functions::NAME();
        self::assertEquals('#NAME?', $result);
    }

    public function testREF()
    {
        $result = Functions::REF();
        self::assertEquals('#REF!', $result);
    }

    public function testNULL()
    {
        $result = Functions::null();
        self::assertEquals('#NULL!', $result);
    }

    public function testVALUE()
    {
        $result = Functions::VALUE();
        self::assertEquals('#VALUE!', $result);
    }

    /**
     * @dataProvider providerIsBlank
     *
     * @param mixed $expectedResult
     */
    public function testIsBlank($expectedResult, ...$args)
    {
        $result = Functions::isBlank(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerN()
    {
        return require 'data/Calculation/Functions/N.php';
    }

    /**
     * @dataProvider providerIsFormula
     *
     * @param mixed $expectedResult
     * @param mixed $reference       Reference to the cell we wish to test
     * @param mixed $value           Value of the cell we wish to test
     */
    public function testIsFormula($expectedResult, $reference, $value = 'undefined')
    {
        $ourCell = null;
        if ($value !== 'undefined') {
            $remoteCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteCell->method('isFormula')
                ->will($this->returnValue(substr($value, 0, 1) == '='));

            $remoteSheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteSheet->method('getCell')
                ->will($this->returnValue($remoteCell));

            $workbook = $this->getMockBuilder(Spreadsheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $workbook->method('getSheetByName')
                ->will($this->returnValue($remoteSheet));

            $sheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $sheet->method('getCell')
                ->will($this->returnValue($remoteCell));
            $sheet->method('getParent')
                ->will($this->returnValue($workbook));

            $ourCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $ourCell->method('getWorksheet')
                ->will($this->returnValue($sheet));
        }

        $result = Functions::isFormula($reference, $ourCell);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIsFormula()
    {
        return require 'data/Calculation/Functions/ISFORMULA.php';
    }

    /**
     * @dataProvider providerIfCondition
     *
     * @param mixed $expectedResult
     */
    public function testIfCondition($expectedResult, ...$args)
    {
        $result = Functions::ifCondition(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIfCondition()
    {
        return require 'data/Calculation/Functions/IF_CONDITION.php';
    }
}
