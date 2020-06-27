<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;

class AddressHelperTest extends TestCase
{
    /**
     * @dataProvider providerR1C1ConversionToA1Absolute
     *
     * @param string $expectedValue
     * @param string $address
     */
    public function testR1C1ConversionToA1Absolute($expectedValue, $address): void
    {
        $actualValue = AddressHelper::convertToA1($address);

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerR1C1ConversionToA1Absolute()
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Absolute.php';
    }

    /**
     * @dataProvider providerR1C1ConversionToA1Relative
     *
     * @param string $expectedValue
     * @param string $address
     * @param ?int $row
     * @param ?int $column
     */
    public function testR1C1ConversionToA1Relative(string $expectedValue, string $address, ?int $row = null, ?int $column = null): void
    {
        $args = [$address];
        if ($row !== null) {
            $args[] = $row;
        }
        if ($column !== null) {
            $args[] = $column;
        }

        $actualValue = AddressHelper::convertToA1(...$args);

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerR1C1ConversionToA1Relative()
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Relative.php';
    }

    /**
     * @dataProvider providerR1C1ConversionToA1Exception
     *
     * @param string $address
     */
    public function testR1C1ConversionToA1Exception($address): void
    {
        $this->expectException(Exception::class);

        AddressHelper::convertToA1($address);
    }

    public function providerR1C1ConversionToA1Exception()
    {
        return require 'tests/data/Cell/R1C1ConversionToA1Exception.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Absolute
     *
     * @param string $expectedValue
     * @param string $address
     */
    public function testA1ConversionToR1C1Absolute($expectedValue, $address): void
    {
        $actualValue = AddressHelper::convertToR1C1($address);

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerA1ConversionToR1C1Absolute()
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Absolute.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Relative
     *
     * @param string $expectedValue
     * @param string $address
     * @param ?int $row
     * @param ?int $column
     */
    public function testA1ConversionToR1C1Relative(string $expectedValue, string $address, ?int $row = null, ?int $column = null): void
    {
        $actualValue = AddressHelper::convertToR1C1($address, $row, $column);

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerA1ConversionToR1C1Relative()
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Relative.php';
    }

    /**
     * @dataProvider providerA1ConversionToR1C1Exception
     *
     * @param string $address
     */
    public function testA1ConversionToR1C1Exception($address): void
    {
        $this->expectException(Exception::class);

        AddressHelper::convertToR1C1($address);
    }

    public function providerA1ConversionToR1C1Exception()
    {
        return require 'tests/data/Cell/A1ConversionToR1C1Exception.php';
    }

}
