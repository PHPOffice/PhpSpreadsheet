<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class OrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerOR
     *
     * @param mixed $expectedResult
     */
    public function testOR($expectedResult, ...$args): void
    {
        $this->runTestCase('OR', $expectedResult, ...$args);
    }

    public static function providerOR(): array
    {
        return require 'tests/data/Calculation/Logical/OR.php';
    }

    /**
     * @dataProvider providerORLiteral
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testORLiteral($expectedResult, $formula): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=OR($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerORLiteral(): array
    {
        return require 'tests/data/Calculation/Logical/ORLiteral.php';
    }
}
