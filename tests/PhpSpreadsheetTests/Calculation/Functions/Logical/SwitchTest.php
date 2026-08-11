<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class SwitchTest extends AllSetupTeardown
{
    #[DataProvider('providerSwitch')]
    public function testSWITCH(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('SWITCH', $expectedResult, ...$args);
    }

    public static function providerSwitch(): array
    {
        return require 'tests/data/Calculation/Logical/SWITCH.php';
    }

    #[DataProvider('providerSwitchArray')]
    public function testIfsArray(array $expectedResult, int $expression, int $value1, string $result1, int $value2, string $result2, string $default): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SWITCH($expression, $value1, {" . "$result1}, $value2, {" . "$result2}, {" . "$default})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerSwitchArray(): array
    {
        return [
            'Array return' => [
                [[4, 5, 6]],
                2,
                1,
                '1, 2, 3',
                2,
                '4, 5, 6',
                '7, 8, 9',
            ],
            'Array return default' => [
                [[7, 8, 9]],
                3,
                1,
                '1, 2, 3',
                2,
                '4, 5, 6',
                '7, 8, 9',
            ],
        ];
    }
}
