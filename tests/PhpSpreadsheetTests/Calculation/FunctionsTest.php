<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $returnDate;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $this->returnDate = Functions::getReturnDateType();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        Functions::setReturnDateType($this->returnDate);
    }

    public function testCompatibilityMode(): void
    {
        $result = Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
        // Test for a true response for success
        self::assertTrue($result);
        // Test that mode has been changed
        self::assertEquals(Functions::COMPATIBILITY_GNUMERIC, Functions::getCompatibilityMode());
    }

    public function testInvalidCompatibilityMode(): void
    {
        $result = Functions::setCompatibilityMode('INVALIDMODE');
        // Test for a false response for failure
        self::assertFalse($result);
        // Test that mode has not been changed
        self::assertEquals(Functions::COMPATIBILITY_EXCEL, Functions::getCompatibilityMode());
    }

    public function testReturnDateType(): void
    {
        $result = Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);
        // Test for a true response for success
        self::assertTrue($result);
        // Test that mode has been changed
        self::assertEquals(Functions::RETURNDATE_PHP_OBJECT, Functions::getReturnDateType());
    }

    public function testInvalidReturnDateType(): void
    {
        $result = Functions::setReturnDateType('INVALIDTYPE');
        // Test for a false response for failure
        self::assertFalse($result);
        // Test that mode has not been changed
        self::assertEquals(Functions::RETURNDATE_EXCEL, Functions::getReturnDateType());
    }

    public function testDUMMY(): void
    {
        $result = Functions::DUMMY();
        self::assertEquals('#Not Yet Implemented', $result);
    }

    public function testDIV0(): void
    {
        $result = Functions::DIV0();
        self::assertEquals('#DIV/0!', $result);
    }

    public function testNA(): void
    {
        $result = Functions::NA();
        self::assertEquals('#N/A', $result);
    }

    public function testNAN(): void
    {
        $result = Functions::NAN();
        self::assertEquals('#NUM!', $result);
    }

    public function testNAME(): void
    {
        $result = Functions::NAME();
        self::assertEquals('#NAME?', $result);
    }

    public function testREF(): void
    {
        $result = Functions::REF();
        self::assertEquals('#REF!', $result);
    }

    public function testNULL(): void
    {
        $result = Functions::null();
        self::assertEquals('#NULL!', $result);
    }

    public function testVALUE(): void
    {
        $result = Functions::VALUE();
        self::assertEquals('#VALUE!', $result);
    }

    /**
     * @dataProvider providerIsBlank
     *
     * @param mixed $expectedResult
     */
    public function testIsBlank($expectedResult, ...$args): void
    {
        $result = Functions::isBlank(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsBlank(): array
    {
        return require 'tests/data/Calculation/Functions/IS_BLANK.php';
    }

    /**
     * @dataProvider providerIsErr
     *
     * @param mixed $expectedResult
     */
    public function testIsErr($expectedResult, ...$args): void
    {
        $result = Functions::isErr(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsErr(): array
    {
        return require 'tests/data/Calculation/Functions/IS_ERR.php';
    }

    /**
     * @dataProvider providerIsError
     *
     * @param mixed $expectedResult
     */
    public function testIsError($expectedResult, ...$args): void
    {
        $result = Functions::isError(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsError(): array
    {
        return require 'tests/data/Calculation/Functions/IS_ERROR.php';
    }

    /**
     * @dataProvider providerErrorType
     *
     * @param mixed $expectedResult
     */
    public function testErrorType($expectedResult, ...$args): void
    {
        $result = Functions::errorType(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerErrorType(): array
    {
        return require 'tests/data/Calculation/Functions/ERROR_TYPE.php';
    }

    /**
     * @dataProvider providerIsLogical
     *
     * @param mixed $expectedResult
     */
    public function testIsLogical($expectedResult, ...$args): void
    {
        $result = Functions::isLogical(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsLogical(): array
    {
        return require 'tests/data/Calculation/Functions/IS_LOGICAL.php';
    }

    /**
     * @dataProvider providerIsNa
     *
     * @param mixed $expectedResult
     */
    public function testIsNa($expectedResult, ...$args): void
    {
        $result = Functions::isNa(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsNa(): array
    {
        return require 'tests/data/Calculation/Functions/IS_NA.php';
    }

    /**
     * @dataProvider providerIsNumber
     *
     * @param mixed $expectedResult
     */
    public function testIsNumber($expectedResult, ...$args): void
    {
        $result = Functions::isNumber(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsNumber(): array
    {
        return require 'tests/data/Calculation/Functions/IS_NUMBER.php';
    }

    /**
     * @dataProvider providerIsText
     *
     * @param mixed $expectedResult
     */
    public function testIsText($expectedResult, ...$args): void
    {
        $result = Functions::isText(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsText(): array
    {
        return require 'tests/data/Calculation/Functions/IS_TEXT.php';
    }

    /**
     * @dataProvider providerIsNonText
     *
     * @param mixed $expectedResult
     */
    public function testIsNonText($expectedResult, ...$args): void
    {
        $result = Functions::isNonText(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsNonText(): array
    {
        return require 'tests/data/Calculation/Functions/IS_NONTEXT.php';
    }

    /**
     * @dataProvider providerIsEven
     *
     * @param mixed $expectedResult
     */
    public function testIsEven($expectedResult, ...$args): void
    {
        $result = Functions::isEven(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsEven(): array
    {
        return require 'tests/data/Calculation/Functions/IS_EVEN.php';
    }

    /**
     * @dataProvider providerIsOdd
     *
     * @param mixed $expectedResult
     */
    public function testIsOdd($expectedResult, ...$args): void
    {
        $result = Functions::isOdd(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsOdd(): array
    {
        return require 'tests/data/Calculation/Functions/IS_ODD.php';
    }

    /**
     * @dataProvider providerTYPE
     *
     * @param mixed $expectedResult
     */
    public function testTYPE($expectedResult, ...$args): void
    {
        $result = Functions::TYPE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTYPE(): array
    {
        return require 'tests/data/Calculation/Functions/TYPE.php';
    }

    /**
     * @dataProvider providerN
     *
     * @param mixed $expectedResult
     */
    public function testN($expectedResult, ...$args): void
    {
        $result = Functions::n(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerN(): array
    {
        return require 'tests/data/Calculation/Functions/N.php';
    }

    /**
     * @dataProvider providerIsFormula
     *
     * @param mixed $expectedResult
     * @param mixed $reference       Reference to the cell we wish to test
     * @param mixed $value           Value of the cell we wish to test
     */
    public function testIsFormula($expectedResult, $reference, $value = 'undefined'): void
    {
        $ourCell = null;
        if ($value !== 'undefined') {
            $remoteCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteCell->method('isFormula')
                ->willReturn(substr($value, 0, 1) == '=');

            $remoteSheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteSheet->method('getCell')
                ->willReturn($remoteCell);

            $workbook = $this->getMockBuilder(Spreadsheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $workbook->method('getSheetByName')
                ->willReturn($remoteSheet);

            $sheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $sheet->method('getCell')
                ->willReturn($remoteCell);
            $sheet->method('getParent')
                ->willReturn($workbook);

            $ourCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $ourCell->method('getWorksheet')
                ->willReturn($sheet);
        }

        $result = Functions::isFormula($reference, $ourCell);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIsFormula(): array
    {
        return require 'tests/data/Calculation/Functions/ISFORMULA.php';
    }

    /**
     * @dataProvider providerIfCondition
     *
     * @param mixed $expectedResult
     */
    public function testIfCondition($expectedResult, ...$args): void
    {
        $result = Functions::ifCondition(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIfCondition(): array
    {
        return require 'tests/data/Calculation/Functions/IF_CONDITION.php';
    }
}
