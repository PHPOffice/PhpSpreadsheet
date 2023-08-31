<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class XorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerXOR
     *
     * @param mixed $expectedResult
     */
    public function testXOR($expectedResult, ...$args): void
    {
        $this->runTestCase('XOR', $expectedResult, ...$args);
    }

    public static function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }

    /**
     * @dataProvider providerXORLiteral
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function xtestXORLiteral($expectedResult, $formula): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=XOR($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerXORLiteral(): array
    {
        return require 'tests/data/Calculation/Logical/XORLiteral.php';
    }
}
