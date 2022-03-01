<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class HypGeomDistTest extends TestCase
{
    /**
     * @dataProvider providerHYPGEOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testHYPGEOMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::HYPGEOMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerHYPGEOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/HYPGEOMDIST.php';
    }

    /**
     * @dataProvider providerHypGeomDistArray
     */
    public function testHypGeomDistArray(
        array $expectedResult,
        string $sampleSuccesses,
        string $sampleNumber,
        string $populationSuccesses,
        string $populationNumber
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=HYPGEOMDIST({$sampleSuccesses}, {$sampleNumber}, {$populationSuccesses}, {$populationNumber})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerHypGeomDistArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.03230668326324188, 0.11602444697599835, 2.7420710766783583E-5],
                    [0.00015615400269340616, 0.1000501002971324, 0.02508542192762165],
                    [7.763976978296478E-9, 0.0013573140575961775, 0.17007598410538344],
                ],
                '{5, 11, 18}',
                '32',
                '{28; 42; 57}',
                '100',
            ],
        ];
    }
}
