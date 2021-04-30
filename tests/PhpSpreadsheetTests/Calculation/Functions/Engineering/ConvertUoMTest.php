<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ConvertUoMTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testGetConversionGroups(): void
    {
        $result = Engineering::getConversionGroups();
        self::assertIsArray($result);
    }

    public function testGetConversionGroupUnits(): void
    {
        $result = Engineering::getConversionGroupUnits();
        self::assertIsArray($result);
    }

    public function testGetConversionGroupUnitDetails(): void
    {
        $result = Engineering::getConversionGroupUnitDetails();
        self::assertIsArray($result);
    }

    public function testGetConversionMultipliers(): void
    {
        $result = Engineering::getConversionMultipliers();
        self::assertIsArray($result);
    }

    public function testGetBinaryConversionMultipliers(): void
    {
        $result = Engineering::getBinaryConversionMultipliers();
        self::assertIsArray($result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     *
     * @param mixed $expectedResult
     */
    public function testCONVERTUOM($expectedResult, ...$args): void
    {
        $result = Engineering::CONVERTUOM(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCONVERTUOM(): array
    {
        return require 'tests/data/Calculation/Engineering/CONVERTUOM.php';
    }
}
