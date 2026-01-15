<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PHPUnit\Framework\TestCase;

/**
 * Tests for DefaultValueBinder::dataTypeForValue().
 *
 * Specifically verifies formula detection behavior after the performance
 * optimization that reuses the Calculation singleton.
 */
class DefaultValueBinderTest extends TestCase
{
    /**
     * Test that valid formulas are correctly identified as TYPE_FORMULA.
     *
     * @dataProvider validFormulaProvider
     */
    public function testValidFormulasReturnTypeFormula(string $formula): void
    {
        $result = DefaultValueBinder::dataTypeForValue($formula);
        self::assertSame(DataType::TYPE_FORMULA, $result);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validFormulaProvider(): array
    {
        return [
            'simple sum' => ['=SUM(A1:A10)'],
            'simple addition' => ['=1+1'],
            'cell reference' => ['=A1'],
            'nested function' => ['=IF(A1>0,SUM(B1:B10),0)'],
            'string concatenation' => ['=A1&B1'],
            'multiplication' => ['=A1*B1'],
            'vlookup' => ['=VLOOKUP(A1,B1:C10,2,FALSE)'],
        ];
    }

    /**
     * Test that invalid or malformed formulas return TYPE_STRING.
     *
     * @dataProvider invalidFormulaProvider
     */
    public function testInvalidFormulasReturnTypeString(string $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame(DataType::TYPE_STRING, $result);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function invalidFormulaProvider(): array
    {
        return [
            'equals sign only' => ['='],
            'equals with space' => ['= '],
            'text starting with equals' => ['=Hello World'],
        ];
    }

    /**
     * Test that null values return TYPE_NULL.
     */
    public function testNullReturnsTypeNull(): void
    {
        $result = DefaultValueBinder::dataTypeForValue(null);
        self::assertSame(DataType::TYPE_NULL, $result);
    }

    /**
     * Test that boolean values return TYPE_BOOL.
     *
     * @dataProvider booleanProvider
     */
    public function testBooleanReturnsTypeBool(bool $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame(DataType::TYPE_BOOL, $result);
    }

    /**
     * @return array<string, array<int, bool>>
     */
    public static function booleanProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }

    /**
     * Test that numeric values return TYPE_NUMERIC.
     *
     * @dataProvider numericProvider
     */
    public function testNumericValuesReturnTypeNumeric(mixed $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame(DataType::TYPE_NUMERIC, $result);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function numericProvider(): array
    {
        return [
            'integer' => [42],
            'negative integer' => [-100],
            'float' => [3.14159],
            'negative float' => [-2.5],
            'zero' => [0],
            'numeric string' => ['123.45'],
            'scientific notation string' => ['1.5e10'],
        ];
    }

    /**
     * Test that regular strings return TYPE_STRING.
     *
     * @dataProvider stringProvider
     */
    public function testStringsReturnTypeString(string $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame(DataType::TYPE_STRING, $result);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function stringProvider(): array
    {
        return [
            'simple text' => ['Hello World'],
            'empty string' => [''],
            'text with numbers' => ['ABC123'],
            'leading zero number' => ['007'],
        ];
    }

    /**
     * Test that error codes return TYPE_ERROR.
     *
     * @dataProvider errorCodeProvider
     */
    public function testErrorCodesReturnTypeError(string $errorCode): void
    {
        $result = DefaultValueBinder::dataTypeForValue($errorCode);
        self::assertSame(DataType::TYPE_ERROR, $result);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function errorCodeProvider(): array
    {
        return [
            'null error' => ['#NULL!'],
            'div zero error' => ['#DIV/0!'],
            'value error' => ['#VALUE!'],
            'ref error' => ['#REF!'],
            'name error' => ['#NAME?'],
            'num error' => ['#NUM!'],
            'na error' => ['#N/A'],
        ];
    }

    /**
     * Test that calling dataTypeForValue multiple times with formulas
     * works correctly (verifies singleton reuse doesn't cause issues).
     */
    public function testMultipleFormulaCallsWorkCorrectly(): void
    {
        // Call multiple times to ensure singleton reuse works
        $result1 = DefaultValueBinder::dataTypeForValue('=SUM(A1:A10)');
        $result2 = DefaultValueBinder::dataTypeForValue('=AVERAGE(B1:B10)');
        $result3 = DefaultValueBinder::dataTypeForValue('=1+2+3');

        self::assertSame(DataType::TYPE_FORMULA, $result1);
        self::assertSame(DataType::TYPE_FORMULA, $result2);
        self::assertSame(DataType::TYPE_FORMULA, $result3);
    }

    /**
     * Test mixed calls to ensure state is properly preserved.
     */
    public function testMixedCallsPreserveCorrectBehavior(): void
    {
        // Interleave formula and non-formula calls
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=A1'));
        self::assertSame(DataType::TYPE_STRING, DefaultValueBinder::dataTypeForValue('Hello'));
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=B2'));
        self::assertSame(DataType::TYPE_NUMERIC, DefaultValueBinder::dataTypeForValue(42));
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=C3*D4'));
    }
}
