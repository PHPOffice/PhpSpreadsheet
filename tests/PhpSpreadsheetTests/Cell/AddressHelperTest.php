<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;

class AddressHelperTest extends TestCase
{
    /**
     * @dataProvider providerR1C1ConversionToA1Absolute
     */
    public function testR1C1ConversionToA1Absolute(string $expectedValue, string $address): void
    {
        $actualValue = AddressHelper::convertToA1($address);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerR1C1ConversionToA1Absolute(): array
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Absolute.php';
    }

    /**
     * @dataProvider providerR1C1ConversionToA1Relative
     */
    public function testR1C1ConversionToA1Relative(
        string $expectedValue,
        string $address,
        ?int $row = null,
        ?int $column = null
    ): void {
        if ($row === null) {
            if ($column === null) {
                $actualValue = AddressHelper::convertToA1($address);
            } else {
                $actualValue = AddressHelper::convertToA1($address, $column);
            }
        } elseif ($column === null) {
            $actualValue = AddressHelper::convertToA1($address, $row);
        } else {
            $actualValue = AddressHelper::convertToA1($address, $row, $column);
        }

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerR1C1ConversionToA1Relative(): array
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Relative.php';
    }

    /**
     * @dataProvider providerR1C1ConversionToA1Exception
     */
    public function testR1C1ConversionToA1Exception(string $address): void
    {
        $this->expectException(Exception::class);

        AddressHelper::convertToA1($address);
    }

    public static function providerR1C1ConversionToA1Exception(): array
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Exception.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Absolute
     */
    public function testA1ConversionToR1C1Absolute(string $expectedValue, string $address): void
    {
        $actualValue = AddressHelper::convertToR1C1($address);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerA1ConversionToR1C1Absolute(): array
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Absolute.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Relative
     */
    public function testA1ConversionToR1C1Relative(string $expectedValue, string $address, ?int $row = null, ?int $column = null): void
    {
        $actualValue = AddressHelper::convertToR1C1($address, $row, $column);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerA1ConversionToR1C1Relative(): array
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Relative.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Exception
     */
    public function testA1ConversionToR1C1Exception(string $address): void
    {
        $this->expectException(Exception::class);

        AddressHelper::convertToR1C1($address);
    }

    public static function providerA1ConversionToR1C1Exception(): array
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Exception.php';
    }

    /**
     * @dataProvider providerConvertFormulaToA1FromSpreadsheetXml
     */
    public function testConvertFormulaToA1SpreadsheetXml(string $expectedValue, string $formula): void
    {
        $actualValue = AddressHelper::convertFormulaToA1($formula);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertFormulaToA1FromSpreadsheetXml(): array
    {
        return require 'tests/data/Cell/ConvertFormulaToA1FromSpreadsheetXml.php';
    }

    /**
     * @dataProvider providerConvertFormulaToA1FromR1C1Absolute
     */
    public function testConvertFormulaToA1R1C1Absolute(string $expectedValue, string $formula): void
    {
        $actualValue = AddressHelper::convertFormulaToA1($formula);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertFormulaToA1FromR1C1Absolute(): array
    {
        return require 'tests/data/Cell/ConvertFormulaToA1FromR1C1Absolute.php';
    }

    /**
     * @dataProvider providerConvertFormulaToA1FromR1C1Relative
     */
    public function testConvertFormulaToA1FromR1C1Relative(string $expectedValue, string $formula, int $row, int $column): void
    {
        $actualValue = AddressHelper::convertFormulaToA1($formula, $row, $column);

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertFormulaToA1FromR1C1Relative(): array
    {
        return require 'tests/data/Cell/ConvertFormulaToA1FromR1C1Relative.php';
    }
}
