<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NotTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerNOT')]
    public function testNOT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('NOT', $expectedResult, ...$args);
    }

    public static function providerNOT(): array
    {
        return require 'tests/data/Calculation/Logical/NOT.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerNotArray')]
    public function testNotArray(array $expectedResult, string $argument1): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NOT({$argument1})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
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
