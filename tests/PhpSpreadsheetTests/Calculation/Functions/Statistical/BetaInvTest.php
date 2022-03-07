<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BetaInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBETAINV
     *
     * @param mixed $expectedResult
     */
    public function testBETAINV($expectedResult, ...$args): void
    {
        $result = Statistical::BETAINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBETAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/BETAINV.php';
    }

    /**
     * @dataProvider providerBetaInvArray
     */
    public function testBetaInvArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BETAINV({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBetaInvArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.24709953547, 0.346789605377], [0.215382947588, 0.307844847105]],
                '0.25',
                '{5, 7.5}',
                '{10; 12}',
            ],
        ];
    }
}
