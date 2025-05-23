<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LogInvTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerLOGINV')]
    public function testLOGINV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('LOGINV', $expectedResult, ...$args);
    }

    public static function providerLOGINV(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGINV.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerLogInvArray')]
    public function testLogInvArray(array $expectedResult, string $probabilities, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOGINV({$probabilities}, {$mean}, {$stdDev})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLogInvArray(): array
    {
        return [
            'row/column vectors' => [
                [[54.598150033144236, 403.4287934927351]],
                '0.5',
                '{4, 6}',
                '7',
            ],
        ];
    }
}
