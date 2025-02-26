<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class XorTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerXOR')]
    public function testXOR(mixed $expectedResult, mixed ...$args): void
    {
        $this->setArrayAsValue();
        $this->runTestCase('XOR', $expectedResult, ...$args);
    }

    public static function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerXORLiteral')]
    public function testXORLiteral(mixed $expectedResult, float|string $formula): void
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
