<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class AndTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAND
     */
    public function testAND(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('AND', $expectedResult, ...$args);
    }

    public static function providerAND(): array
    {
        return require 'tests/data/Calculation/Logical/AND.php';
    }

    /**
     * @dataProvider providerANDLiteral
     */
    public function testANDLiteral(bool|string $expectedResult, float|int|string $formula): void
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
