<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class XorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerXOR
     */
    public function testXOR(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('XOR', $expectedResult, ...$args);
    }

    public static function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }

    /**
     * @dataProvider providerXORLiteral
     */
    public function xtestXORLiteral(mixed $expectedResult, string $formula): void
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
