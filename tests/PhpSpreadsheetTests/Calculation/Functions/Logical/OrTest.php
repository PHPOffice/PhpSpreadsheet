<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class OrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerOR
     */
    public function testOR(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('OR', $expectedResult, ...$args);
    }

    public static function providerOR(): array
    {
        return require 'tests/data/Calculation/Logical/OR.php';
    }

    /**
     * @dataProvider providerORLiteral
     */
    public function testORLiteral(bool|string $expectedResult, float|int|string $formula): void
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
