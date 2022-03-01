<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHER
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testFISHER($expectedResult, $value): void
    {
        $result = Statistical::FISHER($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFISHER(): array
    {
        return require 'tests/data/Calculation/Statistical/FISHER.php';
    }

    /**
     * @dataProvider providerFisherArray
     */
    public function testFisherArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FISHER({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerFisherArray(): array
    {
        return [
            'row vector' => [
                [[-1.4722194895832204, 0.2027325540540821, 0.9729550745276566]],
                '{-0.9, 0.2, 0.75}',
            ],
        ];
    }
}
