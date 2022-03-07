<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TinvTest extends TestCase
{
    /**
     * @dataProvider providerTINV
     *
     * @param mixed $expectedResult
     * @param mixed $probability
     * @param mixed $degrees
     */
    public function testTINV($expectedResult, $probability, $degrees): void
    {
        $result = Statistical::TINV($probability, $degrees);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTINV(): array
    {
        return require 'tests/data/Calculation/Statistical/TINV.php';
    }

    /**
     * @dataProvider providerTInvArray
     */
    public function testTInvArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TINV({$values}, {$degrees})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerTInvArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.29001075058679815, 0.5023133547575189, 0.4713169827948964],
                ],
                '0.65',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
