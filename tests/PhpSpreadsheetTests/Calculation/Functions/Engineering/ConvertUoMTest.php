<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ConvertUoMTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function testGetConversionGroups()
    {
        $result = Engineering::getConversionGroups();
        $this->assertIsArray($result);
    }

    public function testGetConversionGroupUnits()
    {
        $result = Engineering::getConversionGroupUnits();
        $this->assertIsArray($result);
    }

    public function testGetConversionGroupUnitDetails()
    {
        $result = Engineering::getConversionGroupUnitDetails();
        $this->assertIsArray($result);
    }

    public function testGetConversionMultipliers()
    {
        $result = Engineering::getConversionMultipliers();
        $this->assertIsArray($result);
    }

    /**
     * @dataProvider providerCONVERTUOM
     *
     * @param mixed $expectedResult
     */
    public function testCONVERTUOM($expectedResult, ...$args)
    {
        $result = Engineering::CONVERTUOM(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCONVERTUOM()
    {
        return require 'data/Calculation/Engineering/CONVERTUOM.php';
    }
}
