<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class SeriesSumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    public function testSERIESSUM($expectedResult, $arg1, $arg2, $arg3, ...$args): void
    {
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('C1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('C2')->setValue($arg2);
        }
        if ($arg3 !== null) {
            $sheet->getCell('C3')->setValue($arg3);
        }
        $row = 0;
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            ++$row;
            if ($arg !== null) {
                $sheet->getCell("A$row")->setValue($arg);
            }
        }
        $sheet->getCell('B1')->setValue("=SERIESSUM(C1, C2, C3, A1:A$row)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSERIESSUM(): array
    {
        return require 'tests/data/Calculation/MathTrig/SERIESSUM.php';
    }

    /**
     * @dataProvider providerSeriesSumArray
     */
    public function testSeriesSumArray(array $expectedResult, string $x, string $n, string $m, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=SERIESSUM({$x}, {$n}, {$m}, {$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerSeriesSumArray(): array
    {
        return [
            'row vector #1' => [[[3780, 756]], '5', '{1, 0}', '1', '{1, 1, 0, 1, 1}'],
            'column vector #1' => [[[54], [3780]], '{2; 5}', '1', '1', '{1, 1, 0, 1, 1}'],
            'matrix #1' => [[[54, 27], [3780, 756]], '{2; 5}', '{1, 0}', '1', '{1, 1, 0, 1, 1}'],
        ];
    }
}
