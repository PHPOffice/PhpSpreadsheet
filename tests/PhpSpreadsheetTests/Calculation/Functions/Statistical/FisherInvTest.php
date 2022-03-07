<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHERINV
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testFISHERINV($expectedResult, $value): void
    {
        $result = Statistical::FISHERINV($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFISHERINV(): array
    {
        return require 'tests/data/Calculation/Statistical/FISHERINV.php';
    }

    /**
     * @dataProvider providerFisherArray
     */
    public function testFisherArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FISHERINV({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerFisherArray(): array
    {
        return [
            'row vector' => [
                [[-0.7162978701990245, 0.197375320224904, 0.6351489523872873, 0.9051482536448664]],
                '{-0.9, 0.2, 0.75, 1.5}',
            ],
        ];
    }
}
