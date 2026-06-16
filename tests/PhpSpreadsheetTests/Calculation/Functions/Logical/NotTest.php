<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class NotTest extends AllSetupTeardown
{
    #[DataProvider('providerNOT')]
    public function testNOT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('NOT', $expectedResult, ...$args);
    }

    public static function providerNOT(): array
    {
        return require 'tests/data/Calculation/Logical/NOT.php';
    }

    #[DataProvider('providerNotArray')]
    public function testNotArray(array $expectedResult, string $argument1): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NOT({$argument1})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerNotArray(): array
    {
        return [
            'vector' => [
                [[false, true, true, false]],
                '{TRUE, FALSE, FALSE, TRUE}',
            ],
        ];
    }
}
