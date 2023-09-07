<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFDIST
     */
    public function testFDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('F.DIST', $expectedResult, ...$args);
    }

    public static function providerFDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/FDIST.php';
    }

    /**
     * @dataProvider providerFDistArray
     */
    public function testFDistArray(array $expectedResult, string $values, string $u, string $v): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=F.DIST({$values}, {$u}, {$v}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerFDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.005510833927217306, 0.005917159763313607, 0.006191501336451844],
                    [0.0033829117335328167, 0.00291545189504373, 0.0024239018640028246],
                    [0.0027880880388152654, 0.002128148956848886, 0.0015205263468794615],
                ],
                '12',
                '{1, 2, 5}',
                '{2; 4; 5}',
            ],
        ];
    }
}
