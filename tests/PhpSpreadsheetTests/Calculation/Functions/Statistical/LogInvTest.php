<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class LogInvTest extends TestCase
{
    /**
     * @dataProvider providerLOGINV
     *
     * @param mixed $expectedResult
     */
    public function testLOGINV($expectedResult, ...$args): void
    {
        $result = Statistical::LOGINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOGINV(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGINV.php';
    }

    /**
     * @dataProvider providerLogInvArray
     */
    public function testLogInvArray(array $expectedResult, string $probabilities, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOGINV({$probabilities}, {$mean}, {$stdDev})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerLogInvArray(): array
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
