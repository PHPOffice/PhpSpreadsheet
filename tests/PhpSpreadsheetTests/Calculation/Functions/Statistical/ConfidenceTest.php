<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ConfidenceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCONFIDENCE
     */
    public function testCONFIDENCE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('CONFIDENCE', $expectedResult, ...$args);
    }

    public static function providerCONFIDENCE(): array
    {
        return require 'tests/data/Calculation/Statistical/CONFIDENCE.php';
    }

    /**
     * @dataProvider providerConfidenceArray
     */
    public function testConfidenceArray(array $expectedResult, string $alpha, string $stdDev, string $size): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CONFIDENCE({$alpha}, {$stdDev}, {$size})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerConfidenceArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.33261691811208144, 0.6929519127335031, 1.3859038254670062],
                    [0.2351956783344234, 0.48999099653004874, 0.9799819930600975],
                ],
                '0.05',
                '{1.2, 2.5, 5}',
                '{50; 100}',
            ],
        ];
    }
}
