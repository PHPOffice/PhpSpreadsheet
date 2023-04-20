<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class AndTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAND
     *
     * @param mixed $expectedResult
     */
    public function testAND($expectedResult, ...$args): void
    {
        $this->runTestCase('AND', $expectedResult, ...$args);
    }

    public static function providerAND(): array
    {
        return require 'tests/data/Calculation/Logical/AND.php';
    }

    /**
     * @dataProvider providerANDLiteral
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testANDLiteral($expectedResult, $formula): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=AND($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerANDLiteral(): array
    {
        return require 'tests/data/Calculation/Logical/ANDLiteral.php';
    }
}
